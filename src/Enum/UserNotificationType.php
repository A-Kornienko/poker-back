<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum UserNotificationType: string
{
    use ToArray;

    public function getTemplate(): string
    {
        return match ($this) {
            self::TournamentFinish      => 'notifications/finish_tournament.html.twig',
            self::TournamentBeforeStart => 'notifications/before_tournament.html.twig',
            self::TournamentStart       => 'notifications/start_tournament.html.twig',
        };
    }
    case TournamentFinish      = 'tournamentFinish';
    case TournamentBeforeStart = 'tournamentBeforeStart';
    case TournamentStart       = 'tournamentStart';
}
