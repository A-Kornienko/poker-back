<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FlushBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class FlushCombinationHandler extends FlushBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFlushCombinations(...$cards);

        return $combinations ? (new Combination())
            ->setName(CardCombinationRank::Flush->name)
            ->setRank(CardCombinationRank::Flush->value)
            ->setCards(current($combinations)) : null;
    }
}
