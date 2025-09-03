<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Enum\CardCombinationRank;
use App\Handler\Cards\Combination\Base\AbstractBaseCombinationHandler;
use App\ValueObject\{Card, Combination};

class HighCardCombinationHandler extends AbstractBaseCombinationHandler implements CombinationHandlerInterface
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getCombination(Card ...$cards): ?Combination
    {
        $cards = $this->sortCardDesc($cards);

        return (new Combination())
            ->setName(CardCombinationRank::HighCard->name)
            ->setRank(CardCombinationRank::HighCard->value)
            ->setCards(array_slice($cards, 0, 5));
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::HighCard->value;
    }
}
