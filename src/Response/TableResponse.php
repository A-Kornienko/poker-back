<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Table;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableResponse
{
    public static function item(Table $table): array
    {
        return [
            'id'   => $table->getId(),
            'name' => $table->getName(),
            'number' => $table->getNumber(),
        ];
    }

    public static function itemState(Table $table): array
    {
        return [
            'id'                  => $table->getId(),
            'name'                => $table->getName(),
            'type'                => $table->getSetting()->getType()?->value,
            'rule'                => $table->getSetting()->getRule()?->value,
            'style'               => $table->getSetting()->getStyle(),
            'turnPlace'           => $table->getTurnPlace(),
            'lastWordPlace'       => $table->getLastWordPlace(),
            'dealerPlace'         => $table->getDealerPlace(),
            'smallBlindPlace'     => $table->getSmallBlindPlace(),
            'bigBlindPlace'       => $table->getBigBlindPlace(),
            'round'               => $table->getRound()->value,
            'smallBlind'          => $table->getSmallBlind(),
            'bigBlind'            => $table->getBigBlind(),
            'currency'            => $table->getSetting()->getCurrency(),
            'limitPlayers'        => $table->getSetting()->getCountPlayers(),
            'countPlayers'        => count($table->getTableUsers()),
            'image'               => $table->getSetting()->getImage() ? 'table/' . $table->getSetting()->getImage() : ($table->getTournament()?->getImage() ? 'tournament/' . $table->getTournament()?->getImage() : ''),
            'roundExpirationTime' => $table->getRoundExpirationTime() >= time() ? $table->getRoundExpirationTime() - time() : 0,
            'buyIn'               => $table->getSetting()->getBuyIn(),
        ];
    }

    public static function collection(Table ...$tables): array
    {
        $data = [];

        foreach ($tables as $table) {
            $data[] = static::item($table);
        }

        return $data;
    }
}
