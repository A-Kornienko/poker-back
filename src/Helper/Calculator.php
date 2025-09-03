<?php

declare(strict_types=1);

namespace App\Helper;

class Calculator
{
    public static function add($a, $b)
    {
        $result = (($a * 1000) + ($b * 1000)) / 1000;

        return round($result, 2);
    }

    public static function subtract($a, $b)
    {
        $result = (($a * 1000) - ($b * 1000)) / 1000;

        return round($result, 2);
    }

    public static function divide($a, $b)
    {
        $result = ($a * 1000) / ($b * 1000);

        return round($result, 2);
    }

    public static function multiply($a, $b)
    {
        $result = ($a * 1000) * ($b * 1000) / 1000000;

        return round($result, 2);
    }
}
