<?php

namespace App\Enums;

enum Model: string
{
    case User = 'user';

    case Role = 'role';

    case Permission = 'permission';

    case Account = 'account';

    case Client = 'client';
}
