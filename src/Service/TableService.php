<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ReformTableQueue;
use App\Entity\Table;
use App\Entity\TableSetting;
use App\Entity\TableUser;
use App\Entity\Tournament;
use App\Enum\{ReformTableQueueStatus, TableStyle, TableType};
use App\Repository\ReformTableQueueRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;

class TableService
{
    public function __construct(
        protected TableRepository $tableRepository,
        protected EntityManagerInterface $entityManager,
        protected ReformTableQueueRepository $reformTableQueueRepository,
        protected TableSettingService $tableSettingService
    ) {
    }

    public function refreshTable(Table $table): void
    {
        $table
            ->setTurnPlace(0)
            ->setLastWordPlace(0)
            ->removeCards()
            ->setRakeStatus(false);

        $this->entityManager->persist($table);
        $this->entityManager->flush();
    }

    public function remove(Table $table): void
    {
        $this->entityManager->remove($table);
        $this->entityManager->flush();
    }

    public function getEmptyPlaceTablesBySetting(TableSetting $setting): array
    {
        $tables = $this->tableRepository->findBy(['setting' => $setting]);

        // Сортируем столы от большего количества игроков к меньшему
        usort(
            $tables,
            fn($prev, $next) => $next->getTableUsers()->count() <=> $prev->getTableUsers()->count()
        );

        return array_filter(
            $tables,
            fn(Table $table) => $table->getTableUsers()->count() < $table->getSetting()->getCountPlayers()
        );
    }

    public function create(TableSetting $setting): Table
    {
        $number = $this->tableRepository->getMaxNumber($setting) + 1;

        $table = (new Table())
            ->setName($setting->getName() . ' ' . $number)
            ->setBigBlind($setting->getBigBlind())
            ->setSmallBlind($setting->getSmallBlind())
            ->setSetting($setting);

        $this->entityManager->persist($table);
        $this->entityManager->flush();

        return $table;
    }

    public function getCountTablesForTournament(Tournament $tournament): int
    {
        $filteredTournamentUsers = $tournament->getTournamentUsers()->filter(fn($tournamentUser) => !$tournamentUser->getTable());

        return (int) ceil($filteredTournamentUsers->count() / 10);
    }

    public function getFreePlacesGroupedByTables(array $tables): array
    {
        $groupedFreePlaces = [];
        foreach ($tables as $table) {
            //количество свободніх мест в текущем столе итерации
            $countFreePlaces = $table->getCountPlayers() - $table->getTableUsers()->count();

            if ($countFreePlaces > 0) {
                // тут будет массив формата ['2890' => 2] в котором ключ єто id  стола со свободніми местами, а значение єто количество свободніх мест
                $groupedFreePlaces[$table->getId()] = $countFreePlaces;
            }
        }

        return $groupedFreePlaces;
    }

    public function getReformTableQueue(Table $table): ?ReformTableQueue
    {
        return $this->reformTableQueueRepository->findOneBy([
            'table'        => $table,
            'tableSession' => $table->getSession() ?? '',
            'tournament'   => $table->getTournament(),
            'status'       => [ReformTableQueueStatus::Pending,ReformTableQueueStatus::Process],
        ]);
    }

    public function leaveTable(TableUser $player): TableUser
    {
        $player->setLeaver(true)
            ->setUpdatedAt(time());

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    public function createTournamentTable(Tournament $tournament, TableSetting $tableSetting): Table
    {
        $table = new Table();
        $table
            ->setName($tournament->getName() . ' Table ')
            ->setSetting($tableSetting)
            ->setTournament($tournament);

        $tournament->getTables()->add($table);
        $this->entityManager->persist($table);
        $this->entityManager->flush();

        return $table;
    }
}
