<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'identifier',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_user');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'account_client');
    }
}
