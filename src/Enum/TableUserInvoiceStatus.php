<?php

declare(strict_types=1);

namespace App\Enum;

enum TableUserInvoiceStatus: string
{
    use Trait\ToArray;

    case Pending   = 'pending';
    case Completed = 'completed';
    case Back      = 'back';
    case Failed    = 'failed';
}
