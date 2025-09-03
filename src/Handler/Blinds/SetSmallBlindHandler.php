<?php

declare(strict_types=1);

namespace App\Handler\Blinds;

use App\Entity\Table;
use App\Enum\BetType;
use App\Helper\Calculator;
use Doctrine\ORM\EntityManagerInterface;

class SetSmallBlindHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    /**
     * По правилам когда за столом 2 игрока, у кого диллер, у того и малый блайнд.
     */
    protected function setSmallBlindForTwoPlayers(Table $table, array $activePlayersSortedByPlace): Table
    {
        $place         = $table->getDealerPlace();
        $currentPlayer = $activePlayersSortedByPlace[$place];

        // Если не хватает фишек - ставим в аллин
        $smallBlindBet = min($currentPlayer->getStack(), $table->getSmallBlind());
        $stack         = Calculator::subtract($currentPlayer->getStack(), $smallBlindBet);

        $activePlayersSortedByPlace[$place]
            ->setStack($stack)
            ->setBet($smallBlindBet)
            ->setBetType($stack <= 0 ? BetType::AllIn : BetType::SmallBlind);

        $table->setSmallBlindPlace($table->getDealerPlace());
        $this->entityManager->persist($currentPlayer);

        return $table;
    }

    /**
     * По правилам когда за столом больше 2 игроков, малый блайнд ставит первый игрок после диллера.
     */
    protected function setSmallBlindDefault(Table $table, array $activePlayersSortedByPlace): Table
    {
        // Получаем места игроков
        $playerPlaces = array_keys($activePlayersSortedByPlace);
        // Определяем индекс места на котором находится диллер
        $dealerPlaceIndex = array_search($table->getDealerPlace(), $playerPlaces);
        // Определяем какое место первое в списке активных
        $firstPlaceNumber = array_key_first($activePlayersSortedByPlace);
        // Определяем что место диллера не последнее среди активных.
        $isSmallBlindPlaceNotLast = $dealerPlaceIndex !== false && array_key_exists($dealerPlaceIndex + 1, $playerPlaces);
        // Определяем следующее место после диллера, которое и будет являться малым блайндом.
        $smallBlindPlace = $isSmallBlindPlaceNotLast ? $playerPlaces[$dealerPlaceIndex + 1] : $firstPlaceNumber;
        // Устанавливаем значения для пользовтеля и стола.
        $currentPlayer = $activePlayersSortedByPlace[$smallBlindPlace];

        // Если не хватает фишек - ставим в аллин
        $smallBlindBet      = min($currentPlayer->getStack(), $table->getSmallBlind());
        $currentPlayerChips = Calculator::subtract($currentPlayer->getStack(), $smallBlindBet);

        $currentPlayer->setStack($currentPlayerChips)
            ->setBet($smallBlindBet)
            ->setBetType($currentPlayerChips <= 0 ? BetType::AllIn : BetType::SmallBlind);

        $table->setSmallBlindPlace($smallBlindPlace);
        $this->entityManager->persist($currentPlayer);

        return $table;
    }

    public function __invoke(Table $table, array $activePlayersSortedByPlace): Table
    {
        if (!$table->getDealerPlace()) {
            return $table;
        }

        $smallBlind = $table->getTournament() ? $table->getTournament()->getSmallBlind() : $table->getSmallBlind();
        $table->getSetting()->setSmallBlind($smallBlind);

        return count($activePlayersSortedByPlace) < 3
            ? $this->setSmallBlindForTwoPlayers($table, $activePlayersSortedByPlace)
            : $this->setSmallBlindDefault($table, $activePlayersSortedByPlace);
    }
}
