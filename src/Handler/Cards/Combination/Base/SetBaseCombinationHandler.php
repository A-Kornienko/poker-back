<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;

class SetBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    public function getSetCombinations(Card ...$cards): ?array
    {
        $cards               = $this->sortCardDesc($cards);
        $groupedCardsByValue = $this->groupCardsByValue($cards);

        $sets = [];
        foreach ($groupedCardsByValue as $groupedCards) {
            if (count($groupedCards) < 3) {
                continue;
            }

            $sets = [...$sets, ...$this->combinations($groupedCards, 3)];
        }

        if (!count($sets)) {
            return null;
        }

        $combinations = [];
        foreach ($sets as $set) {
            $remainingCards = array_diff($cards, $set);
            $remainingCards = $this->sortCardDesc($remainingCards);

            $kickerCardCombinations = $this->combinations($remainingCards, 2);
            foreach ($kickerCardCombinations as $kickerCardCombination) {
                $combinations[] = [...$set, ...$kickerCardCombination];
            }
        }

        return count($combinations) ? $combinations : null;
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::Set->value;
    }
}
