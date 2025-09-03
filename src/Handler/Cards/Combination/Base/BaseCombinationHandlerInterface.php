<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\Base;

use App\ValueObject\{Card, Combination};

interface BaseCombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination;

    public static function getDefaultPriority(): int;
}
