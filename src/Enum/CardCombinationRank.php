<?php

declare(strict_types=1);

namespace App\Enum;

enum CardCombinationRank: int
{
    case RoyalFlush    = 10;
    case StraightFlush = 9;
    case Four          = 8;
    case FullHouse     = 7;
    case Flush         = 6;
    case Straight      = 5;
    case Set           = 4;
    case TwoPairs      = 3;
    case Pair          = 2;
    case HighCard      = 1;
}
