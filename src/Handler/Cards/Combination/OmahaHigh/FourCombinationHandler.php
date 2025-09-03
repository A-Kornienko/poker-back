<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\FourBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class FourCombinationHandler extends FourBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getFourCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::Four->name)
                        ->setRank(CardCombinationRank::Four->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
