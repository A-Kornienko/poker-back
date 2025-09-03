<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FullHouseBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class FullHouseCombinationHandler extends FullHouseBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFullHouseCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::FullHouse->name)
                        ->setRank(CardCombinationRank::FullHouse->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
