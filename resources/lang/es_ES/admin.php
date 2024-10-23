<?php

return [
    'nav' => [
        'title' => 'Discord auth',
        'settings' => 'Configuración',
    ],

    'permission' => 'Ver y cambiar la configuración de discord-auth',

    'settings' => [
        'title' => 'Configuración de Discord Auth',
        'header' => 'Gestión de autenticación a través de Discord',
        'subtitle' => 'Tu aplicación de Discord',
        'discord-portal' => 'Guardar una aplicación de Discord',
        'info' => "En Discord Developers, en la sección OAuth -> Redirects, no olvides añadir:",

        'client_id' => 'ID del Cliente',
        'client_secret' => 'Secreto del Cliente',
        'guild' => 'ID del servidor (déjelo vacío si desea permitir que el usuario inicie sesión aunque no esté presente en su servidor de Discord)',
        'guild_invite' => 'Enlace de invitación (déjelo vacío si desea permitir que el usuario inicie sesión aunque no esté presente en su servidor de Discord)',
        'avatar' => "Tener el avatar de Discord en la cuenta de Azuriom (se actualiza en cada inicio de sesión en el sitio web)",
    ],
];
