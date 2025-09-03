<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum TableState: string
{
    use ToArray;

    case Init    = 'init';
    case Sync    = 'sync';
    case Start   = 'start';
    case Run     = 'run';
    case Finish  = 'finish';
    case Refresh = 'refresh';
}
