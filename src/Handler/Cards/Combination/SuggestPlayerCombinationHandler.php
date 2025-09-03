<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination;

use App\Entity\{Table, User};
use App\Enum\CardCombinationRank;
use App\Repository\TableUserRepository;

class SuggestPlayerCombinationHandler
{
    public function __construct(
        protected TableUserRepository $tableUserRepository,
        protected GetCombinationHandler $getCombinationHandler
    ) {
    }

    public function __invoke(Table $table, ?User $user): string
    {
        if (!$user) {
            return '';
        }

        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user->getId(),
            'table' => $table->getId()
        ]);

        if (!$player) {
            return '';
        }

        $cards = array_merge($player->getCards(), $player->getTable()->getCards());

        if (!$cards) {
            return '';
        }

        $combination = ($this->getCombinationHandler)($table, ...$cards);

        $combinationName  = $combination->getName();
        $combinationCards = $combination->getCards();

        $selectedCards = match ($combinationName) {
            CardCombinationRank::RoyalFlush->value    => array_slice($combinationCards, 0, 5),
            CardCombinationRank::StraightFlush->value => array_slice($combinationCards, 0, 5),
            CardCombinationRank::Four->value          => [$combinationCards[0]],
            CardCombinationRank::FullHouse->value     => [$combinationCards[0], $combinationCards[3]],
            CardCombinationRank::Flush->value         => array_slice($combinationCards, 0, 5),
            CardCombinationRank::Straight->value      => array_slice($combinationCards, 0, 5),
            CardCombinationRank::Set->value           => [$combinationCards[0]],
            CardCombinationRank::TwoPairs->value      => [$combinationCards[0], $combinationCards[2]],
            CardCombinationRank::Pair->value          => [$combinationCards[0]],
            default                                   => [$combinationCards[0]]
        };

        $combinationCards = implode(' ', array_map(fn($card) => $card->getView(), $selectedCards));

        return $combinationName . ', ' . $combinationCards;
    }
}
