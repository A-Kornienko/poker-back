<?php

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FullHouseBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class FullHouseCombinationHandler extends FullHouseBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFullHouseCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::FullHouse->name)
            ->setRank(CardCombinationRank::FullHouse->value)
            ->setCards(current($combinations)) : null;
    }
}
