<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;

class TwoPairsBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    public function getTwoPairsCombinations(Card ...$cards): ?array
    {
        $cards               = $this->sortCardDesc($cards);
        $groupedCardsByValue = $this->groupCardsByValue($cards);

        $pairs = [];
        foreach ($groupedCardsByValue as $groupedCards) {
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
            $remainingCards               = array_diff($cards, $pair);
            $remainingCards               = $this->sortCardDesc($remainingCards);
            $groupedRemainingCardsByValue = $this->groupCardsByValue($remainingCards);

            foreach ($groupedRemainingCardsByValue as $groupedRemainingCards) {
                if (count($groupedRemainingCards) < 2) {
                    continue;
                }

                $secondPairs = $this->combinations($groupedRemainingCards, 2);
                foreach ($secondPairs as $secondPair) {
                    $remainingCardForKicker = array_diff($remainingCards, $secondPair);
                    $remainingCardForKicker = $this->sortCardDesc($remainingCardForKicker);

                    foreach ($remainingCardForKicker as $kicker) {
                        $combinations[] = [...$pair, ...$secondPair, $kicker];
                    }
                }
            }
        }

        return count($combinations) ? $combinations : null;
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::TwoPairs->value;
    }
}
