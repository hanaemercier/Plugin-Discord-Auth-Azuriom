<?php

namespace Azuriom\Plugin\DiscordAuth\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DiscordAuth\Models\Discord;
use Azuriom\Plugin\DiscordAuth\Models\User;
use Azuriom\Rules\GameAuth;
use Azuriom\Rules\Username;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthHomeController extends Controller
{
    private $guild;

    public function __construct()
    {
        config(["services.discord.client_id" => setting('discord-auth.client_id', '')]);
        config(["services.discord.client_secret" => setting('discord-auth.client_secret', '')]);
        config(["services.discord.redirect_id" => "/discord-auth/callback"]);
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
            'name' => ['required', 'string', 'max:25', 'unique:users', new Username(), new GameAuth()]
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

        session()->put('socialite_callback', 'from_controller');

        return Socialite::driver('discord')
            ->scopes('guilds')->redirect();
    }

    private function hasRightGuild($guilds)
    {
        if ($this->guild == '') {
            return true;
        }

        $found = false;

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
            $user = Socialite::driver('discord')->user();
    
            $guilds = Http::withToken($user->token)
                ->get('https://discord.com/api/users/@me/guilds')
                ->throw()
                ->json();
    
            if (!$this->hasRightGuild($guilds)) {
                return redirect()->route('discord-auth.guild');
            }
    
            $discordId = $user->user['id'];
            $email = $user->user['email'];
            $created = false;
        
            $discords = Discord::with('user')
                ->where('discord_id', $discordId)
                ->orderByDesc('id')
                ->get();
        
            if ($discords->isEmpty() || $discords->first()->user->is_deleted) {
        
                if (Auth::guest() && User::where('email', $email)->exists()) {
                    $redirect = redirect();
                    $redirect->setIntendedUrl(route('discord-auth.login'));
                    return $redirect
                        ->route('login')
                        ->with('error', trans('discord-auth::messages.email_already_exists'));
                } elseif (Auth::user()) {
                    $userToLogin = Auth::user();
                } else {
                    $userToLogin = User::forceCreate([
                        'name' => $discordId,
                        'email' => $email,
                        'password' => Hash::make(Str::random(32)),
                        'last_login_ip' => $request->ip(),
                        'last_login_at' => now(),
                    ]);
        
                    $created = true;
                }
        
                $discord = new Discord();
                $discord->discord_id = $discordId;
                $discord->user_id = $userToLogin->id;
                $discord->save();
            } else {
                $userToLogin = $discords->first()->user;
            }
        
            if ($userToLogin->isBanned()) {
                throw ValidationException::withMessages([
                    'email' => trans('auth.suspended'),
                ])->redirectTo(URL::route('login'));
            }
        
            if (setting('maintenance-status', false) && !$userToLogin->can('maintenance.access')) {
                return $this->sendMaintenanceResponse($request);
            }
        
            $this->guard()->login($userToLogin, true);
        
            if ($created) {
                return redirect()->route('discord-auth.username');
            }
        
            if ($userToLogin->name === $discordId) {
                return redirect()->route('discord-auth.username');
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

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Get the maintenance response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendMaintenanceResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => trans('auth.maintenance')], 503);
        }

        return redirect()->back()->with('error', trans('auth.maintenance'));
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
