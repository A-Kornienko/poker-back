<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;

class PairBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    public function getPairCombinations(Card ...$cards): ?array
    {
        // Сортируем карты по убыванию.
        $cards               = $this->sortCardDesc($cards);
        $groupedCardsByValue = $this->groupCardsByValue($cards);

        $pairs = [];
        // Находим все пары
        foreach ($groupedCardsByValue as $groupedCards) {
            // Если в группе меньше 2 карт не пара
            if (count($groupedCards) < 2) {
                continue;
            }

            $pairs = [...$pairs, ...$this->combinations($groupedCards, 2)];
        }

        if (!count($pairs)) {
            return null;
        }

        $combinations = [];
        foreach ($pairs as $pair) {
            $remainingCards = array_diff($cards, $pair);
            $remainingCards = $this->sortCardDesc($remainingCards);

            $kickerCardCombinations = $this->combinations($remainingCards, 3);
            foreach ($kickerCardCombinations as $kickerCardCombination) {
                $combinations[] = [...$pair, ...$kickerCardCombination];
            }
        }

        return count($combinations) ? $combinations : null;
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::Pair->value;
    }
}
