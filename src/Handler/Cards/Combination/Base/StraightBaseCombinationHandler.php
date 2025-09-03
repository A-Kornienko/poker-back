<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\Card as EnumCard;
use App\Enum\CardCombinationRank;
use App\ValueObject\Card;

class StraightBaseCombinationHandler extends AbstractBaseStraightCombinationHandler
{
    public function getStraightCombinations(Card ...$cards): ?array
    {
        $cards        = $this->sortCardDesc($cards);
        $combinations = $this->getAllStraightCombinations($cards, 5);

        if ($combinations) {
            return $combinations;
        }

        // Find small straight
        foreach ($cards as $card) {
            if ($card->getName() === EnumCard::Ace->name && $card->getValue() === EnumCard::Ace->value) {
                $card->setValue(1);
            }
        }

        $cards        = $this->sortCardDesc($cards);
        $combinations = $this->getAllStraightCombinations($cards, 5);

        return count($combinations) ? $combinations : null;
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::Straight->value;
    }
}
