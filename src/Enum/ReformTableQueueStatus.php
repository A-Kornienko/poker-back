<?php

declare(strict_types=1);

namespace App\Enum;

enum ReformTableQueueStatus: string
{
    use Trait\ToArray;

    case Pending   = 'pending';
    case Process   = 'process';
    case Completed = 'completed';
}
