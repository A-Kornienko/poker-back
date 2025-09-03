<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\SetBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class SetCombinationHandler extends SetBaseCombinationHandler implements CombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getSetCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::Set->name)
                        ->setRank(CardCombinationRank::Set->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
