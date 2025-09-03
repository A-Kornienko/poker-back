<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\User;
use App\ValueObject\TableState;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableStateResponse
{
    public static function item(TableState $tableState, TranslatorInterface $translator, ?User $user): array
    {
        return [
            ...TableResponse::itemState($tableState->getTable()),
            'players' => PlayerResponse::tableStateCollection($tableState->getPlayers()),
            'banks'   => BankResponse::tableStateCollection($tableState->getBanks()),
            'cards'   => [
                'table'  => $tableState->getCards()['table'] ? CardResponse::collection(...$tableState->getCards()['table']) : [],
                'player' => $tableState->getCards()['player'] ? CardResponse::collection(...$tableState->getCards()['player']) : [],
            ],
            'countCards'         => $tableState->getTable()->getSetting()->getRule()->countPlayerCards(),
            'betNavigation'      => $tableState->getBetNavigation(),
            'betRange'           => $tableState->getBetRange(),
            'myPlace'            => $tableState->getMyPlace(),
            'myPrize'            => $tableState->getMyPrize(),
            'spectators'         => $tableState->getSpectators(),
            'maxBet'             => $tableState->getMaxBet() >= $tableState->getTable()->getSetting()->getBigBlind() ? $tableState->getMaxBet() : $tableState->getTable()->getSetting()->getBigBlind(),
            'session'            => $tableState->getTable()->getSession() ? md5($tableState->getTable()->getSession()) : null,
            'tournament'         => $tableState->getTable()?->getTournament() ? TournamentResponse::item($tableState->getTable()?->getTournament(), $translator) : [],
            'playerSetting'      => $user?->getPlayerSetting() ? PlayerSettingResponse::item($user?->getPlayerSetting()) : [],
            'state'              => $tableState->getTable()->getState(),
            'suggestCombination' => $tableState->getSuggestCombination(),
        ];
    }
}
