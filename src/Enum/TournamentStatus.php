<?php

declare(strict_types=1);

namespace App\Enum;

enum TournamentStatus: string
{
    use Trait\ToArray;

    public static function getPublic(): array
    {
        return [
            TournamentStatus::Pending->value  => TournamentStatus::Pending->name,
            TournamentStatus::Started->value  => TournamentStatus::Started->name,
            TournamentStatus::Finished->value => TournamentStatus::Finished->name,
            TournamentStatus::Break->value    => TournamentStatus::Break->name,
            TournamentStatus::Canceled->value => TournamentStatus::Canceled->name,
        ];
    }

    case Pending  = 'pending';
    case Started  = 'started';
    case Finished = 'finished';
    case Break    = 'break';
    case Canceled = 'canceled';
    case Sync     = 'sync';
}
