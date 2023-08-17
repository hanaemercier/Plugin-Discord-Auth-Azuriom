<?php

return [
    'nav' => [
        'title' => 'Discord auth',
        'settings' => 'Einstellungen',
    ],

    'permission' => 'Discord-Auth-Einstellungen anzeigen und ändern',

    'settings' => [
        'title' => 'Einstellungen Discord Auth',
        'header' => 'Verwaltung der Authentifizierung über Discord',
        'subtitle' => 'Ihre Discord-Anwendung',
        'discord-portal' => 'Lege eine Discord APP an',
        'info' => 'Auf Discord Developers, im Abschnitt OAuth -> Redirects, vergiss nicht hinzuzufügen :',

        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret',
        'guild' => 'Guild ID (leer lassen, wenn du dem Benutzer erlauben möchtest, sich anzumelden, auch wenn er nicht auf deinem Gildenserver vorhanden ist)',
        'guild_invite' => 'Einladungslink von Ihrem Discord-Server (leer lassen, wenn du dem Benutzer erlauben möchtest, sich anzumelden, auch wenn er nicht auf deinem Gildenserver vorhanden ist)',
        'avatar' => "Das Discord-Avatar auf dem Azuriom-Konto haben (aktualisiert sich bei jeder Anmeldung auf der Website)",
    ],
];
