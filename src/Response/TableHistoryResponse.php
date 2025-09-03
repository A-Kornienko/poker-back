<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\TableHistory;
use App\ValueObject\Card;
use App\ValueObject\TableHistory\PlayerTableHistory;
use App\ValueObject\TableHistory\RoundActionTableHistory;
use App\ValueObject\TableHistory\WinnerTableHistory;

class TableHistoryResponse
{
    public static function item(TableHistory $tableHistory)
    {
        return [
            'session' => $tableHistory->getSession(),
            'cards'   => array_map(fn(Card $card) => $card->toArray(), $tableHistory->getCards()),
            'players' => $tableHistory->getPlayers(),
            'blinds'  => $tableHistory->getBlinds()->toArray(),
            'dealer'  => $tableHistory->getDealer(),
            'preflop' => array_map(fn(RoundActionTableHistory $roundAction) => $roundAction->toArray(), $tableHistory->getPreflop()),
            'flop'    => array_map(fn(RoundActionTableHistory $roundAction) => $roundAction->toArray(), $tableHistory->getFlop()),
            'turn'    => array_map(fn(RoundActionTableHistory $roundAction) => $roundAction->toArray(), $tableHistory->getTurn()),
            'river'   => array_map(fn(RoundActionTableHistory $roundAction) => $roundAction->toArray(), $tableHistory->getRiver()),
            'pot'     => $tableHistory->getPot()->toArray(),
            'winners' => array_map(fn(WinnerTableHistory $winner) => $winner->toArray(), $tableHistory->getWinners()),
        ];
    }
}
