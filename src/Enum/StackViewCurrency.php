<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum StackViewCurrency: string
{
    use ToArray;
    case Dollar   = '$';
    case BigBlind = 'BB';
}
