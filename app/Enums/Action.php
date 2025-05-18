<?php

namespace App\Enums;

enum Action: string
{
    case ViewAny = 'viewAny';

    case View = 'view';

    case Create = 'create';

    case Update = 'update';

    case Delete = 'delete';

    case Restore = 'restore';

    case ForceDelete = 'forceDelete';
}
