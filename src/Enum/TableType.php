<?php

declare(strict_types=1);

namespace App\Enum;

enum TableType: string
{
    use Trait\ToArray;

    case Tournament = 'tournament';
    case Cash       = 'cash';
}
