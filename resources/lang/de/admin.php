<?php

return [
    'nav' => [
        'title' => 'Discord auth',
        'settings' => 'Einstellungen',
    ],

    'permission' => 'Discord-Auth-Einstellungen anzeigen und ändern',

    'settings' => [
        'title' => 'Einstellungen Discord Auth',
        'header' => 'Verwaltung der Authentifizierung via Discord',
        'subtitle' => 'Ihre Discord-Anwendung',
        'discord-portal' => 'Lege eine Discord APP an',
        'info' => 'Nicht vergessen einen Redirect im Discord Developers Portal unter OAuth -> Redirects anzulegen:',

        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret',
        'guild' => 'Guild ID (leer lassen, wenn du dem Benutzer eine Anmeldung erlauben möchtest, ohne das dieser deinem Discordserver beigetreten sein muss)',
        'guild_invite' => 'Discord Einladungslink (leer lassen, wenn du dem Benutzer eine Anmeldung erlauben möchtest, ohne das dieser deinem Discordserver beigetreten sein muss)',
        'avatar' => "Discord-Avatar mit Azuriom synchronisieren (aktualisiert sich bei jeder Anmeldung auf der Website)",
    ],
];
