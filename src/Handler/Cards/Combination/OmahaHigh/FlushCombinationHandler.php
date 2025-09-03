<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FlushBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class FlushCombinationHandler extends FlushBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFlushCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::Flush->name)
                        ->setRank(CardCombinationRank::Flush->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
