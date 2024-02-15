<?php

namespace Azuriom\Plugin\DiscordAuth\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the discord-auth settings page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show()
    {
        return view('discord-auth::admin.settings', [
            'client_id' => setting('discord-auth.client_id', ''),
            'client_secret' => setting('discord-auth.client_secret', ''),
            'guild' => setting('discord-auth.guild', ''),
            'guild_invite' => setting('discord-auth.guild_invite', ''),
            'avatar' => setting('discord-auth.avatar', ''),
        ]);
    }

    public function save(Request $request)
    {
        $validated = $this->validate($request, [
            'client_id' => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255'],
            'guild' => ['nullable', 'string', 'max:255'],
            'guild_invite' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ]);

        $avatar = $validated['avatar'] === '1' ? 'on' : 'off';

        Setting::updateSettings([
            'discord-auth.client_id' => $validated['client_id'],
            'discord-auth.client_secret' => $validated['client_secret'],
            'discord-auth.guild' => $validated['guild'],
            'discord-auth.guild_invite' => $validated['guild_invite'],
            'discord-auth.avatar' => $avatar,
            'discord-auth.url' => url()->to('/'),
        ]);

        return redirect()->route('discord-auth.admin.settings')->with('success', trans('admin.settings.updated'));
    }
}
