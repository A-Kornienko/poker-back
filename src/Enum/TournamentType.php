<?php

declare(strict_types=1);

namespace App\Enum;

enum TournamentType: string
{
    use Trait\ToArray;

    case Free = 'free';
    case Paid = 'paid';
}
