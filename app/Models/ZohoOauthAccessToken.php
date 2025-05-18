<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoOauthAccessToken extends Model
{
    protected $fillable = [
        'access_token', 'expires_at', 'revoked', 'scopes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];
}
