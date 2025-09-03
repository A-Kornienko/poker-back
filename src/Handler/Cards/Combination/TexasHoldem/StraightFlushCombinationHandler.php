<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\StraightFlushBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class StraightFlushCombinationHandler extends StraightFlushBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getStraightFlushCombinations(...$cards);

        if ($combinations) {
            return (new Combination())
                ->setName(CardCombinationRank::StraightFlush->name)
                ->setRank(CardCombinationRank::StraightFlush->value)
                ->setCards(current($combinations));
        }

        $this->rollbackAceValues(...$cards);

        return null;
    }
}
