<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_client');
    }
}
