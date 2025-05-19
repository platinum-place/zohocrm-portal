<?php

namespace App\Enums\User;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case SUPERVISOR = 'supervisor';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('enums.admin'),
            self::SUPERVISOR => __('enums.supervisor'),
        };
    }
}
