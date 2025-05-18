<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoOauthRefreshToken extends Model
{
    protected $fillable = [
        'refresh_token',
    ];
}
