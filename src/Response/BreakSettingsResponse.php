<?php

declare(strict_types=1);

namespace App\Response;

use App\Helper\DateTimeHelper;
use App\Helper\HoursAndMinutesHelper;
use App\ValueObject\BreakSettings;

class BreakSettingsResponse
{
    public static function item(BreakSettings $BreakSettings): array
    {
        return [
            'period'   => HoursAndMinutesHelper::formatted($BreakSettings->getPeriod()),
            'lastTime' => DateTimeHelper::formatted($BreakSettings->getLastTime(), 'H:i'),
            'duration' => HoursAndMinutesHelper::formatted($BreakSettings->getDuration())
        ];
    }
}
