<?php

declare(strict_types=1);

namespace App\Helper;

class HoursAndMinutesHelper
{
    public static function formatted(?int $seconds): ?string
    {
        if (!$seconds) {
            return null;
        }

        $data = '';

        $minutes = $seconds / 60;
        $hours   = $minutes / 60;

        $residue = (int) $minutes % 60;

        if ($hours >= 1) {
            $data .= $hours . ' hours';
        }

        if ($residue >= 1) {
            $data .= ' ' . $residue . ' minutes';
        }

        return $data;
    }
}
