<?php

return [
    'nav' => [
        'title' => 'Discord auth',
        'settings' => 'Paramètres',
    ],

    'permission' => 'Voir et modifier les paramètres du plugin Discord-auth',

    'settings' => [
        'title' => 'Paramètres Discord Auth',
        'header' => "Gestion de l'authentification via Discord",
        'subtitle' => 'Votre application discord',
        'discord-portal' => 'Enregistrer une application Discord',
        'info' => "Sur Discord Developers, dans la section OAuth -> Redirects, n'oubliez pas d'ajouter :",

        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret',
        'guild' => 'Identifiant de votre guild (laisser vide pour autoriser des membres qui ne sont pas de votre guild à se connecter)',
    ],
];
