<?php

declare(strict_types=1);

namespace App\Enum;

enum PlayerPosition: string
{
    case Button         = 'BU';
    case SmallBlind     = 'SB';
    case BigBlind       = 'BB';
    case UnderTheGun    = 'UTG';
    case MiddlePosition = 'MP';
    case CutOff         = 'CO';
}
