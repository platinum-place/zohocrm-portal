<?php

namespace App\Models\Zoho;

use Illuminate\Database\Eloquent\Model;

class ZohoOauthRefreshToken extends Model
{
    protected $fillable = [
        'refresh_token',
    ];
}
