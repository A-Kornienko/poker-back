<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRole: string
{
    use Trait\ToArray;

    case Player = 'ROLE_PLAYER';
    case Admin  = 'ROLE_ADMIN';
}
