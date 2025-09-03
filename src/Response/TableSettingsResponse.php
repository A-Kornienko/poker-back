<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Table;
use App\Entity\TableSetting;

class TableSettingsResponse
{
    public static function item(TableSetting $tableSetting): array
    {
        return [
            'buyIn'        => $tableSetting->getBuyIn(),
            'countTables'  => $tableSetting->getTables()->filter(fn(Table $table) => !$table->getIsArchived())->count(),
            'countPlayers' => array_sum($tableSetting->getTables()->map(fn(Table $table) => $table->getTableUsers()->count())->getValues()),
            'bigBlind'     => $tableSetting->getBigBlind(),
            'smallBlind'   => $tableSetting->getSmallBlind(),
            'limitPlayers' => $tableSetting->getCountPlayers(),
            'settingId'    => $tableSetting->getId(),
        ];
    }

    public static function details(TableSetting $tableSetting): array
    {
        return [
            'id'           => $tableSetting->getId(),
            'name'         => $tableSetting->getName(),
            'type'         => $tableSetting->getType()?->value,
            'rule'         => $tableSetting->getRule()?->value,
            'style'        => $tableSetting->getStyle(),
            'buyIn'        => $tableSetting->getBuyIn(),
            'turnTime'     => $tableSetting->getTurnTime(),
            'smallBlind'   => $tableSetting->getSmallBlind(),
            'bigBlind'     => $tableSetting->getBigBlind(),
            'currency'     => $tableSetting->getCurrency(),
            'limitPlayers' => $tableSetting->getCountPlayers(),
            'image'        => $tableSetting->getImage() ?: 'table/' . $tableSetting->getImage(),
            'rake'         => $tableSetting->getRake(),
            'rakeCap'      => $tableSetting->getRakeCap(),
            'timeBank'     => $tableSetting->getTimeBank(),
        ];
    }

    public static function collection(TableSetting ...$tableSettings): array
    {
        $data = [];

        foreach ($tableSettings as $setting) {
            $data[] = static::item($setting);
        }

        return $data;
    }
}
