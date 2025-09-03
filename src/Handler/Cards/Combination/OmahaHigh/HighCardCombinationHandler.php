<?php

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Enum\CardCombinationRank;
use App\Enum\CardType;
use App\Handler\Cards\Combination\Base\AbstractBaseCombinationHandler;
use App\ValueObject\Card;
use App\ValueObject\Combination;

class HighCardCombinationHandler extends AbstractBaseCombinationHandler implements CombinationHandlerInterface
{
    public function getCombination(Card ...$cards): ?Combination
    {
        $cards = $this->sortCardDesc($cards);

        // Разделяем карты на карманные (hand) и общие (table).
        $handKickers  = array_filter($cards, fn($card) => $card->getType() === CardType::Hand);
        $tableKickers = array_filter($cards, fn($card) => $card->getType() === CardType::Table);

        // Объединяем первые 2 карманные карты и первые 3 общие карты.
        $combination = $this->sortCardDesc(array_merge(
            array_slice($handKickers, 0, 2),
            array_slice($tableKickers, 0, 3)
        ));

        // Возвращаем комбинацию из 5 лучших карт.
        return (new Combination())
            ->setName(CardCombinationRank::HighCard->name)
            ->setRank(CardCombinationRank::HighCard->value)
            ->setCards($combination);
    }

    public static function getDefaultPriority(): int
    {
        return CardCombinationRank::HighCard->value;
    }
}
