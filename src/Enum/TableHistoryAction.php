<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum TableHistoryAction: string
{
    use ToArray;

    case Bet           = 'bet';
    case Winner        = 'winner';
    case CalculateBank = 'calculateBank';
    case ChangeRound   = 'changeRound';
    case ActivePlayers = 'activePlayers';
}
