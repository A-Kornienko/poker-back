<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Handler\Cards\Combination\Base\RoyalFlushBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class RoyalFlushCombinationHandler extends RoyalFlushBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $combination = parent::getCombination(...$cards);

        if ($combination && $this->isOmahaRule($combination->getCards())) {
            return $combination;
        }

        return null;
    }
}
