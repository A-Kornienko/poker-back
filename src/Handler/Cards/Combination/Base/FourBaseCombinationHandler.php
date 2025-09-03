<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;

class FourBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    /**
     * @param Card ...$cards
     */
    public function getFourCombinations(Card ...$cards): ?array
    {
        // Сортируем карты по убыванию
        $cards               = $this->sortCardDesc($cards);
        $groupedCardsByValue = $this->groupCardsByValue($cards);
        $fours               = array_filter(
            array_map(
                fn($groupedCards) => count($groupedCards) > 3 ? $groupedCards : null,
                $groupedCardsByValue
            )
        );

        if (!count($fours)) {
            return null;
        }

        $combinations = [];
        foreach ($fours as $four) {
            $remainingCards = array_diff($cards, $four);
            $remainingCards = $this->sortCardDesc($remainingCards);
            foreach ($remainingCards as $remainingCard) {
                $combinations[] = [...$four, $remainingCard];
            }
        }

        return $combinations;
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::Four->value;
    }
}
