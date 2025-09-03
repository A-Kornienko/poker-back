<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Bank;

class BankResponse
{
    public static function tableStateCollection(array $banks, ?array $winners = []): array
    {
        $response = [
            'rake'  => 0,
            'items' => [],
        ];

        if (!$banks) {
            return $response;
        }

        foreach ($banks as $bank) {
            $response['items'][$bank->getId()] = static::tableStateItem($bank);
            $response['rake'] += $bank->getRake();
        }

        return $response;
    }

    public static function tableStateItem(Bank $bank): array
    {
        $winnerPlaces = [];
        foreach ($bank->getWinners() as $winner) {
            $winnerPlaces[] = $winner->getTableUser()->getPlace();
        }

        return [
            'sum'     => $bank->getSum(),
            'winners' => $winnerPlaces,
        ];
    }
}
