<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\StraightFlushBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

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
            foreach ($combinations as $combination) {
                if ($this->isOmahaRule($combination)) {
                    return (new Combination())
                        ->setName(CardCombinationRank::StraightFlush->name)
                        ->setRank(CardCombinationRank::StraightFlush->value)
                        ->setCards($combination);
                }
            }
        }

        $this->rollbackAceValues(...$cards);

        return null;
    }
}
