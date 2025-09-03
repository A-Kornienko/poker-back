<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination;

class CompareCombinationsHandler
{
    public function __invoke(array $combinationCardsA, array $combinationCardsB): int
    {
        for ($i = 0; $i < 5; $i++) {
            if ($combinationCardsA[$i]->getValue() !== $combinationCardsB[$i]->getValue()) {
                // Если значения не равны, возвращаем разницу между ними
                return $combinationCardsB[$i]->getValue() - $combinationCardsA[$i]->getValue();
            }
        }

        // Если все значения равны, возвращаем 0
        return 0;
    }
}
