<?php

namespace Azuriom\Plugin\DiscordAuth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Discord
 *
 * @property $id
 * @property $discord_id
 * @property $user_id
 *
 * @package Azuriom\Plugin\DiscordAuth\Controllers\Models
 */
class Discord extends Model
{

    public function user() {
        return $this->belongsTo(User::class);
    }
}
