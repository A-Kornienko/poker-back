<?php

declare(strict_types=1);

namespace App\Enum;

enum TournamentSendEmailAction: string
{
    case Start      = 'start';
    case End        = 'end';
    case Register   = 'register';
    case Unregister = 'unregister';
    case PlayerRank = 'playerRank';
    case Cancel     = 'cancel';
}
