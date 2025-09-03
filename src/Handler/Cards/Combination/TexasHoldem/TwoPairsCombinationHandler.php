<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\TwoPairsBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class TwoPairsCombinationHandler extends TwoPairsBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getTwoPairsCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::TwoPairs->name)
            ->setRank(CardCombinationRank::TwoPairs->value)
            ->setCards(current($combinations)) : null;
    }
}
