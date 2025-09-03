<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardType;

abstract class AbstractBaseCombinationHandler
{
    public function combinations(array $cards, int $count = 2): array
    {
        $result = [];

        if ($count == 0) {
            return [[]];
        }

        foreach ($cards as $key => $card) {
            $cardCombination = array_slice($cards, $key + 1);
            $subCombinations = $this->combinations($cardCombination, $count - 1);

            foreach ($subCombinations as $subCombination) {
                array_unshift($subCombination, $card);
                $result[] = $subCombination;
            }
        }

        return $result;
    }

    /**
     * @param array $cards
     * @return array
     */
    protected function sortCardDesc(array $cards): array
    {
        usort(
            $cards,
            fn($prev, $next) => $next->getValue() <=> $prev->getValue()
        );

        return $cards;
    }

    protected function isOmahaRule(array $combination): bool
    {
        $hand  = 0;
        $table = 0;
        foreach ($combination as $card) {
            if ($card->getType()->value === CardType::Hand->value) {
                $hand++;

                continue;
            }

            $table++;
        }

        return ($hand === 2 && $table === 3);
    }

    protected function groupCardsByValue(array $cards): array
    {
        $groupedCardsByValue = [];
        foreach ($cards as $card) {
            $groupedCardsByValue[$card->getValue()][] = $card;
        }

        return $groupedCardsByValue;
    }

    protected function groupCardsBySuit(array $cards): array
    {
        $groupedCardsBySuit = [];
        foreach ($cards as $card) {
            $groupedCardsBySuit[$card->getSuit()->value][] = $card;
        }

        return $groupedCardsBySuit;
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return 0;
    }
}
