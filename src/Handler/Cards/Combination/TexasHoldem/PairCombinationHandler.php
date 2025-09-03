<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\PairBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class PairCombinationHandler extends PairBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getPairCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::Pair->name)
            ->setRank(CardCombinationRank::Pair->value)
            ->setCards(current($combinations)) : null;
    }
}
