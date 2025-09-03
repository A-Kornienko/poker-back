<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum TableStyle: string
{
    use ToArray;

    case TableBlue   = 'table_blue';
    case TableRed    = 'table_red';
    case TableGreen  = 'table_green';
    case TableOrange = 'table_orange';
}
