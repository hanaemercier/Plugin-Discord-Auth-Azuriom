<?php

namespace Azuriom\Plugin\DiscordAuth\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DiscordAuth\Models\Discord;
use Azuriom\Plugin\DiscordAuth\Models\User;
use Azuriom\Models\Setting;
use Azuriom\Rules\GameAuth;
use Azuriom\Rules\Username;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DiscordAuthHomeController extends Controller
{
    private $guild;

    public function __construct()
    {
        $this->guild = setting('discord-auth.guild', '');
    }

    public function guild(Request $request)
    {
        $isController = $request->session()->get('socialite_callback') === 'from_controller';

        session()->put('socialite_callback', 'from_controller');

        if (!$isController || Auth::check()) {
            return redirect('/');
        }

        return view('discord-auth::guild');
    }

    public function username()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        $discord = Discord::where('user_id', $user->id)->first();

        if ($user->name !== $discord->discord_id) {
            return redirect('/');
        }

        return view('discord-auth::username', ['conditions' => setting('conditions')]);
    }

    public function registerUsername(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:25', 'unique:users', new Username(), new GameAuth()],
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        return redirect()->route('home');
    }

    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        Session::put('socialite_callback', 'from_controller');

        $discordAuthUrl =
            "https://discord.com/oauth2/authorize" .
            "?client_id=" .
            setting('discord-auth.client_id') .
            "&redirect_uri=" .
            urlencode(config('app.url') . "/discord-auth/callback") .
            "&response_type=code" .
            "&scope=identify%20email%20guilds";

        return redirect($discordAuthUrl);
    }

    private function hasRightGuild($guilds)
    {
        if ($this->guild == '') {
            return true;
        }

        foreach ($guilds as $guild) {
            if ($guild['id'] == $this->guild) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtain the user information from Discord.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws ValidationException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handleProviderCallback(Request $request)
    {
        $isController = $request->session()->get('socialite_callback') === 'from_controller';

        if (!$isController) {
            return redirect('/');
        }

        if (Auth::check()) {
            return redirect('/');
        }

        try {
            $API_ENDPOINT = 'https://discord.com/api/v10';

            $clientId = setting('discord-auth.client_id');
            $clientSecret = setting('discord-auth.client_secret');

            $redirectUrl = urlencode(route('discord-auth.callback'));
            $redirectUrl = str_replace('%2F', '/', $redirectUrl);
            $redirectUrl = str_replace('%3A', ':', $redirectUrl);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $API_ENDPOINT . '/oauth2/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                http_build_query([
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $request->input('code'),
                    'redirect_uri' => $redirectUrl,
                    'scope' => 'identify email guilds',
                ])
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response1 = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response1, true);

            if (!isset($response['access_token'])) {
                return redirect()->route('discord-auth.login');
            }

            $accessToken = $response['access_token'];

            $userUrl = $API_ENDPOINT . '/users/@me';
            $userHeaders = ['Authorization: Bearer ' . $accessToken];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $userHeaders);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $userResponse = curl_exec($ch);
            curl_close($ch);

            $user = json_decode($userResponse, true);

            $guildsUrl = $API_ENDPOINT . '/users/@me/guilds';
            $guildsHeaders = ['Authorization: Bearer ' . $accessToken];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $guildsUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $guildsHeaders);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $guildsResponse = curl_exec($ch);
            curl_close($ch);

            $guilds = json_decode($guildsResponse, true);

            if (!$this->hasRightGuild($guilds)) {
                return redirect()->route('discord-auth.guild');
            }

            $discordId = $user['id'];
            $email = $user['email'];

            $discords = Discord::with('user')
                ->where('discord_id', $discordId)
                ->orderByDesc('id')
                ->get();

            if ($discords->isEmpty() || $discords->first()->user->is_deleted) {
                if (User::where('email', $email)->exists()) {
                    $discord = new Discord();
                    $discord->discord_id = $discordId;
                    $discord->user_id = User::where('email', $email)->value('id');
                    $discord->save();

                    Auth::loginUsingId($discord->user_id);
                } else {
                    $userToLogin = User::forceCreate([
                        'name' => $discordId,
                        'email' => $email,
                        'password' => Hash::make(Str::random(32)),
                        'last_login_ip' => $request->ip(),
                        'last_login_at' => now(),
                    ]);

                    if (setting('discord-auth.avatar') === "on") {
                        $userToLogin->avatar = "https://cdn.discordapp.com/avatars/" . $discordId . "/" . $user['avatar'] . "?size=1024";
                        $userToLogin->save();
                    }

                    $discord = new Discord();
                    $discord->discord_id = $discordId;
                    $discord->user_id = $userToLogin->id;
                    $discord->save();

                    Auth::loginUsingId($userToLogin->id);
                }
            } else {
                $userToLogin = $discords->first()->user;
            }

            $this->guard()->login($userToLogin, true);

            if ($userToLogin->name === $discordId) {
                return redirect()->route('discord-auth.username');
            }

            if (setting('discord-auth.avatar') === "on") {
                $userToLogin->avatar = "https://cdn.discordapp.com/avatars/" . $discordId . "/" . $user['avatar'] . "?size=1024";
                $userToLogin->save();
            } else {
                $userToLogin->avatar = null;
                $userToLogin->save();
            }

            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect('/');
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson() ? new JsonResponse([], 204) : redirect()->intended($this->redirectPath());
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
