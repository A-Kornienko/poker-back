<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\ReformTableQueue;
use App\Entity\Table;
use App\Enum\ReformTableQueueStatus;
use App\Handler\AbstractHandler;
use App\Repository\ReformTableQueueRepository;
use App\Repository\TableRepository;
use App\Service\PlayerService;
use App\Service\TableService;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReformTablesHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected ReformTableQueueRepository $reformTableQueueRepository,
        protected TableRepository $tableRepository,
        protected TournamentService $tournamentService,
        protected TableService $tableService,
        protected PlayerService $playerService
    ) {
        parent::__construct($security, $translator);
    }

    /**
     * @throws ORMException
     */
    protected function movePlayersToCurrentTable(array $otherTables, Table $table): void
    {
        // Получаем пустые места стола, за который будем сажать игроков других столов
        $freePlaces      = $table->getFreePlaces();
        $countTableUsers = $table->getTableUsers()->count();

        // Считаем общее количество игроков
        $countOtherPlayers = array_sum(array_map(fn(Table $table) => $table->getTableUsers()->count(), $otherTables));

        // Вычисляем среднее количество игроков за столом (с учетом стола, за который будем сажать других игроков)
        $avgFreePlaces = (int)round(($countOtherPlayers + $countTableUsers) / (count($otherTables) + 1));

        // Проходимся по столам, из которых будем забирать игроков
        foreach ($otherTables as $tableWithEmptyPlaces) {
            $tableUsers = $tableWithEmptyPlaces->getTableUsers();
            //Вісчитіваем количество игроков которое будет пересажено.
            $countMovingPlayers = $tableUsers->count() - $avgFreePlaces;

            //забираем определенное количество игроков и пересаживаем за наш стол
            foreach ($tableUsers->slice(0, $countMovingPlayers) as $player) {
                if ($countTableUsers === $avgFreePlaces) {
                    break;
                }

                $place = array_shift($freePlaces);
                $this->playerService->changeTable($player, $table, $place);

                $countTableUsers++;
            }
        }
    }

    /**
     * @throws ORMException
     */
    protected function movePlayersToAnotherTables(array $otherTables, Table $table): void
    {
        $players = $table->getTableUsers()->toArray();

        /**
         * @var Table $emptyPlacesTable
         */
        //перебираем столі котоіре имеют пустіе места
        foreach ($otherTables as $emptyPlacesTable) {
            //массив из номеров свободніх мест
            $freePlaces = $emptyPlacesTable->getFreePlaces();

            //перебираем игроков которіх будет пересаживать
            foreach ($players as $key => $player) {
                if (empty($freePlaces)) {
                    break;
                }

                //забираем из массива свободніх мест, одно место.
                $place = array_shift($freePlaces);
                //садим игрока за другой стол.

                $player->setBet(0)
                    ->setBetSum(0)
                    ->setBetType(null)
                    ->setBetExpirationTime(0)
                    ->removeCards();

                $this->playerService->changeTable($player, $emptyPlacesTable, $place);
                //убираем игрока из массива игроков
                unset($players[$key]);
            }

            if (empty($players)) {
                break;
            }
        }

        $table->setIsArchived(true);

        $this->entityManager->persist($table);
        $this->entityManager->flush();
    }

    protected function moveToAnotherProcess(ReformTableQueue $reformTableQueue, Table $table, array $tournamentTablesForSeat): bool
    {
        $freePlacesGroupedByTables = $this->tableService->getFreePlacesGroupedByTables($tournamentTablesForSeat);

        if (array_sum($freePlacesGroupedByTables) >= $table->getTableUsers()->count()) {
            $reformTableQueue->setData(
                array_map(fn($takePlayerTable) => $takePlayerTable->getId(), $tournamentTablesForSeat)
            );
            $this->entityManager->persist($reformTableQueue);
            $this->entityManager->flush();

            $this->movePlayersToAnotherTables($tournamentTablesForSeat, $table);

            $reformTableQueue->setStatus(ReformTableQueueStatus::Completed);
            $this->entityManager->persist($reformTableQueue);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    protected function moveToCurrentProcess(ReformTableQueue $reformTableQueue, Table $table, array $excludedTableIds): bool
    {
        $takePlayerTables = $this->tableRepository->getTablesForTakePlayers($table->getTournament()->getId(), $excludedTableIds);
        $reformTableQueue->setData(
            array_map(fn($takePlayerTable) => $takePlayerTable->getId(), $takePlayerTables)
        );
        $this->entityManager->persist($reformTableQueue);
        $this->entityManager->flush();

        $this->movePlayersToCurrentTable($takePlayerTables, $table);

        if($table->getTableUsers()->count() >= $this->tournamentService->getAvgPlayersInTableForTournament($table->getTournament())) {
            $reformTableQueue->setStatus(ReformTableQueueStatus::Completed);
            $this->entityManager->persist($reformTableQueue);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function __invoke(ReformTableQueue $reformTableQueue): void
    {
        $reformTableQueue->setStatus(ReformTableQueueStatus::Process);
        $this->entityManager->persist($reformTableQueue);
        $this->entityManager->flush();

        $table = $reformTableQueue->getTable();

        $otherReformTableQueues = $this->reformTableQueueRepository->getOtherReformTableQueues($table, $reformTableQueue);

        $excludedTableIds = [$table->getId()];

        foreach ($otherReformTableQueues as $otherReformTableQueue) {
            $excludedTableIds = array_merge($excludedTableIds, (array)$otherReformTableQueue->getData());
        }

        try {
            $tournamentTablesForSeat = $this->tableRepository->getTournamentTablesForSeat($table->getTournament()->getId(), $excludedTableIds);

            if ($this->moveToAnotherProcess($reformTableQueue, $table, $tournamentTablesForSeat)) {
                return;
            }

            if ($this->moveToCurrentProcess($reformTableQueue, $table, $excludedTableIds)) {
                return;
            }

            $reformTableQueue->setStatus(ReformTableQueueStatus::Pending)
                ->setData([]);
            $this->entityManager->persist($reformTableQueue);
            $this->entityManager->flush();
        } catch (\Exception|ORMException $e) {
            $reformTableQueue->setStatus(ReformTableQueueStatus::Pending)
                ->setData([]);
            $this->entityManager->persist($reformTableQueue);
            $this->entityManager->flush();
        }
    }
}
