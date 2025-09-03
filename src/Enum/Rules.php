<?php

declare(strict_types=1);

namespace App\Enum;

enum Rules: string
{
    use Trait\ToArray;
    use Trait\FromName;

    public function countPlayerCards(): int
    {
        return match($this) {
            Rules::OmahaHigh => 4,
            default          => 2,
        };
    }
    case TexasHoldem = 'Texas Holdem';
    case OmahaHigh   = 'Omaha High';
}
