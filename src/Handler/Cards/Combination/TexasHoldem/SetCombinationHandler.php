<?php

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\SetBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class SetCombinationHandler extends SetBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getSetCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::Set->name)
            ->setRank(CardCombinationRank::Set->value)
            ->setCards(current($combinations)) : null;
    }
}
