<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\TournamentPrize;

class TournamentPrizeResponse
{
    public static function collection(TournamentPrize ...$tournamentPrizes): array
    {
        $response = [];
        foreach ($tournamentPrizes as $tournamentPrize) {
            $response[] = static::item($tournamentPrize);
        }

        return $response;
    }

    public static function item(TournamentPrize $tournamentPrize): array
    {
        return [
            'id'   => $tournamentPrize->getId(),
            'name' => $tournamentPrize->getWinner()?->getLogin(),
            'sum'  => $tournamentPrize->getSum(),
        ];
    }
}
