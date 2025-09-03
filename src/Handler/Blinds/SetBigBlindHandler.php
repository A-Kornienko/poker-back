<?php

declare(strict_types=1);

namespace App\Handler\Blinds;

use App\Entity\Table;
use App\Enum\{BetType, TableUserStatus};
use App\Helper\Calculator;
use Doctrine\ORM\EntityManagerInterface;

class SetBigBlindHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    /**
     * По правилам когда за столом 2 игрока, большой блайнд у первого игрока после диллера.
     */
    protected function setBigBlindForTwoPlayers(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        unset($activeTableUsersSortedByPlace[$table->getDealerPlace()]);
        $firstPlaceNumber = (int) array_key_first($activeTableUsersSortedByPlace);
        $currentTableUser = $activeTableUsersSortedByPlace[$firstPlaceNumber];

        // Если не хватает фишек - ставим в аллин
        $bigBlindBet = min($currentTableUser->getStack(), $table->getBigBlind());
        $stack       = Calculator::subtract($currentTableUser->getStack(), $bigBlindBet);

        $currentTableUser->setStack($stack)
            ->setBet($bigBlindBet)
            ->setBetType($stack <= 0 ? BetType::AllIn : BetType::BigBlind);

        $table->setBigBlindPlace($firstPlaceNumber);
        $this->entityManager->persist($currentTableUser);

        return $table;
    }

    protected function setBigBlindDefault(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        // Получаем места игроков
        $playerPlaces = array_keys($activeTableUsersSortedByPlace);
        // Определяем индекс места на котором находится малый блайнд
        $smallBlindPlaceIndex = array_search($table->getSmallBlindPlace(), $playerPlaces);
        // Определяем какое место первое в списке активных
        $firstPlaceNumber = array_key_first($activeTableUsersSortedByPlace);
        // Определяем что место малого блайнда не последнее среди активных.
        $isBigBlindPlaceNotLast = $smallBlindPlaceIndex !== false && array_key_exists($smallBlindPlaceIndex + 1, $playerPlaces);
        // Определяем следующее место после малого блайнда, которое и будет являться большим блайндом.
        $bigBlindPlace = $isBigBlindPlaceNotLast ? $playerPlaces[$smallBlindPlaceIndex + 1] : $firstPlaceNumber;
        // Устанавливаем значения для пользовтеля и стола.
        $currentTableUser = $activeTableUsersSortedByPlace[$bigBlindPlace];

        // Если не хватает фишек - ставим в аллин
        $bigBlindBet           = min($currentTableUser->getStack(), $table->getBigBlind());
        $currentTableUserChips = Calculator::subtract($currentTableUser->getStack(), $bigBlindBet);

        $currentTableUser->setStack($currentTableUserChips)
            ->setBet($bigBlindBet)
            ->setBetType($currentTableUserChips <= 0 ? BetType::AllIn : BetType::BigBlind);

        if ($currentTableUser->getStatus() === TableUserStatus::WaitingBB) {
            $currentTableUser->setStatus(TableUserStatus::Active);
        }

        $table->setBigBlindPlace($bigBlindPlace);
        $this->entityManager->persist($currentTableUser);

        return $table;
    }

    public function __invoke(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        if (!$table->getSmallBlindPlace()) {
            return $table;
        }

        $bigBlind = $table->getTournament() ? $table->getTournament()->getBigBlind() : $table->getBigBlind();
        $table->getSetting()->setBigBlind($bigBlind);

        return count($activeTableUsersSortedByPlace) < 3
            ? $this->setBigBlindForTwoPlayers($table, $activeTableUsersSortedByPlace)
            : $this->setBigBlindDefault($table, $activeTableUsersSortedByPlace);
    }
}
