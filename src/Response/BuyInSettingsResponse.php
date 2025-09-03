<?php

declare(strict_types=1);

namespace App\Response;

use App\Helper\HoursAndMinutesHelper;
use App\ValueObject\BuyInSettings;

class BuyInSettingsResponse
{
    public static function item(BuyInSettings $buyInSettings): array
    {
        return [
            'sum'                       => $buyInSettings->getSum(),
            'chips'                     => $buyInSettings->getChips(),
            'limit_by_number_of_times'  => $buyInSettings->getLimitByNumberOfTimes(),
            'limit_by_chips_in_percent' => $buyInSettings->getLimitByChipsInPercent() . '%',
            'limit_by_time_formatted'   => HoursAndMinutesHelper::formatted($buyInSettings->getLimitByTime()),
            'limit_by_time'             => $buyInSettings->getLimitByTime(),
            'limit_by_count_players'    => $buyInSettings->getLimitByCountPlayers(),
        ];
    }
}
