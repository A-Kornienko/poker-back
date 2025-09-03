<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\PairBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class PairCombinationHandler extends PairBaseCombinationHandler implements CombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getPairCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::Pair->name)
                        ->setRank(CardCombinationRank::Pair->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
