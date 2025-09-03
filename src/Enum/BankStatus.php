<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum BankStatus: string
{
    use ToArray;
    case InProgress = 'inProgress';
    case Completed  = 'completed';
}
