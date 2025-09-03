<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class FullHouseBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getFullHouseCombinations(Card ...$cards): ?array
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
            $remainingCards               = array_diff($cards, $set);
            $remainingCards               = $this->sortCardDesc($remainingCards);
            $groupedRemainingCardsByValue = $this->groupCardsByValue($remainingCards);

            foreach ($groupedRemainingCardsByValue as $groupedRemainingCards) {
                if (count($groupedRemainingCards) < 2) {
                    continue;
                }

                $pairs = $this->combinations($groupedRemainingCards, 2);
                foreach ($pairs as $pair) {
                    $combinations[] = [...$set, ...$pair];
                }
            }
        }

        return count($combinations) ? $combinations : null;
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::FullHouse->value;
    }
}
