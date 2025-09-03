<?php

namespace App\Helper;

use DateTimeZone;

class DateTimeHelper
{
    public static function formatted(?int $timeStamp, string $format = 'Y-m-d H:i:s', $timezone = 'UTC'): string
    {
        return (new \DateTime())->setTimestamp($timeStamp ?? 0)
            ->setTimezone(new DateTimeZone($timezone))
            ->format($format);
    }
}
