<?php

declare(strict_types=1);

namespace App\Enum;

enum CardType: string
{
    case Hand  = 'hand';
    case Table = 'table';
}
