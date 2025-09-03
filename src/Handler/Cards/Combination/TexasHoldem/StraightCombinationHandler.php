<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\StraightBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class StraightCombinationHandler extends StraightBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getStraightCombinations(...$cards);

        if ($combinations) {
            return (new Combination())
                ->setName(CardCombinationRank::Straight->name)
                ->setRank(CardCombinationRank::Straight->value)
                ->setCards(current($combinations));
        }

        $this->rollbackAceValues(...$cards);

        return null;
    }
}
