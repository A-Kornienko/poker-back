<?php

declare(strict_types=1);

namespace App\Enum;

enum TableUserStatus: string
{
    use Trait\ToArray;

    case AutoBlind    = 'autoBlind';
    case WaitingBB = 'waitingBB';
    case Pending      = 'pending';
    case Active       = 'active';
    case Lose         = 'lose';
    case Winner       = 'winner';
}
