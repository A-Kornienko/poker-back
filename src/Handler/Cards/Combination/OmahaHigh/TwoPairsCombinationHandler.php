<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\TwoPairsBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class TwoPairsCombinationHandler extends TwoPairsBaseCombinationHandler implements CombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination
    {
        $combinations = $this->getTwoPairsCombinations(...$cards);

        if ($combinations) {
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::TwoPairs->name)
                        ->setRank(CardCombinationRank::TwoPairs->value)
                        ->setCards($combination);
                }
            }
        }

        return null;
    }
}
