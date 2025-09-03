<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\CardCombinationRank;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class FlushBaseCombinationHandler extends AbstractBaseCombinationHandler
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getFlushCombinations(Card ...$cards): ?array
    {
        $cards              = $this->sortCardDesc($cards);
        $groupedCardsBySuit = $this->groupCardsBySuit($cards);

        $applicableSuits = array_filter(
            array_map(fn($cards) => count($cards) > 4 ? $cards : null, $groupedCardsBySuit)
        );

        if (count($applicableSuits) < 1) {
            return null;
        }

        return $this->combinations(current($applicableSuits), 5);
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::Flush->value;
    }
}
