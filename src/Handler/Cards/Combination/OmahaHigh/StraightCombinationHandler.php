<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\StraightBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class StraightCombinationHandler extends StraightBaseCombinationHandler implements CombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getStraightCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::Straight->name)
                        ->setRank(CardCombinationRank::Straight->value)
                        ->setCards($combination);
                }
            }
        }

        $this->rollbackAceValues(...$cards);

        return null;
    }
}
