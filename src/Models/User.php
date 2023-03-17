<?php

namespace Azuriom\Plugin\DiscordAuth\Models;


class User extends \Azuriom\Models\User
{
    public function discord() {
        return $this->belongsTo(Discord::class);
    }
}
