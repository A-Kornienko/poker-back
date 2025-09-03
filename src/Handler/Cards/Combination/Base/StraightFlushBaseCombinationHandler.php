<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\Base;

use App\Enum\Card as EnumCard;
use App\Enum\CardCombinationRank;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class StraightFlushBaseCombinationHandler extends AbstractBaseStraightCombinationHandler
{
    /**
     * @param Card ...$cards
     * @return Combination|null
     */
    public function getStraightFlushCombinations(Card ...$cards): ?array
    {
        $cards              = $this->sortCardDesc($cards);
        $groupedCardsBySuit = $this->groupCardsBySuit($cards);

        $applicableSuits = [];
        foreach ($groupedCardsBySuit as $suitCards) {
            if (count($suitCards) < 5) {
                continue;
            }

            $applicableSuits[] = $suitCards;
        }

        if (!count($applicableSuits)) {
            return null;
        }

        foreach ($applicableSuits as $applicableSuit) {
            $combinations = $this->getAllStraightCombinations($applicableSuit, 5);
            if (!count($combinations)) {
                continue;
            }

            return $combinations;
        }

        // Find small straight
        reset($applicableSuits);
        $combinations = [];
        foreach ($applicableSuits as $applicableSuit) {
            $applicableSuit = array_map(function ($card) {
                if ($card->getName() === EnumCard::Ace->name && $card->getValue() === EnumCard::Ace->value) {
                    $card->setValue(1);
                }

                return $card;
            }, $applicableSuit);

            $applicableSuit = $this->sortCardDesc($applicableSuit);
            $combinations   = [...$combinations, ...$this->getAllStraightCombinations($applicableSuit, 5)];
        }

        return count($combinations) ? $combinations : null;
    }

    /**
     * @return int
     */
    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::StraightFlush->value;
    }
}
