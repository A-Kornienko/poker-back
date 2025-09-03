<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FourBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class FourCombinationHandler extends FourBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFourCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::Four->name)
            ->setRank(CardCombinationRank::Four->value)
            ->setCards(current($combinations)) : null;
    }
}
