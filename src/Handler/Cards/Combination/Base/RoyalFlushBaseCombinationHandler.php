<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class RoyalFlushBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    protected const COMBINATION_VALUES = [14, 13, 12, 11, 10];

    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        // Сортируем карты по возрастанию
        $cards              = $this->sortCardDesc($cards);
        $groupedCardsBySuit = $this->groupCardsBySuit($cards);
        $applicableSuits    = array_filter(
            array_map(
                fn($cards) => count($cards) > 4 ? $cards : null,
                $groupedCardsBySuit
            )
        );

        if (count($applicableSuits) < 1) {
            return null;
        }

        $combinations = $this->combinations(current($applicableSuits), 5);
        foreach ($combinations as $combination) {
            $combinationValues = array_map(fn(Card $card) => $card->getValue(), $combination);

            if (array_values($combinationValues) === array_values(self::COMBINATION_VALUES)) {
                return (new Combination())
                    ->setName(CardCombinationRank::RoyalFlush->name)
                    ->setRank(CardCombinationRank::RoyalFlush->value)
                    ->setCards($combination);
            }
        }

        return null;
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::RoyalFlush->value;
    }
}
