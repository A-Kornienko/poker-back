<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Table;
use App\Entity\Tournament;
use App\Enum\Round;
use App\Enum\TournamentStatus;
use App\Handler\AbstractHandler;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SynchronizationTournamentTablesHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableUserRepository $tableUserRepository,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct($security, $translator);
    }

    protected function isTableSynchronizationEnabled(Tournament $tournament): bool
    {
        return $tournament->getSetting()->getTableSynchronization()
            && $tournament->getStatus() !== TournamentStatus::Break;
    }

    protected function getActiveTableIds(Tournament $tournament): array
    {
        return $tournament->getTables()
            ->filter(fn(Table $table) => !$table->getIsArchived())
            ->map(fn(Table $table) => $table->getId())->getValues();
    }

    protected function hasMoreActiveUsersThanPrizes(Tournament $tournament, array $tableIds): bool
    {
        $activeTournamentUsers = $this->tableUserRepository->findBy(['table' => $tableIds]);

        return count($activeTournamentUsers) > $tournament->getPrizes()->count();
    }

    protected function shouldSyncTournament(Tournament $tournament): bool
    {
        return $tournament->getStatus() !== TournamentStatus::Sync;
    }

    protected function allTablesHaveFinished(Tournament $tournament, array $tableIds): bool
    {
        $tablePendingIds = $tournament->getTables()
            ->filter(fn(Table $table) => !$table->getIsArchived())
            ->map(fn($table) => $table->getRound() === Round::PreFlop ? $table->getId() : null)->getValues();

        $diffTables = array_diff($tableIds, array_filter($tablePendingIds));

        return count($diffTables) < 1;
    }

    protected function setTournamentStatus(Tournament $tournament, TournamentStatus $status): void
    {
        $tournament->setStatus($status);
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
    }

    public function __invoke(Tournament $tournament): void
    {
        if (!$this->isTableSynchronizationEnabled($tournament)) {
            return;
        }

        $tableIds = $this->getActiveTableIds($tournament);

        if (count($tableIds) < 2) {
            $this->setTournamentStatus($tournament, TournamentStatus::Started);

            return;
        }

        if ($this->hasMoreActiveUsersThanPrizes($tournament, $tableIds)) {
            $this->setTournamentStatus($tournament, TournamentStatus::Started);

            return;
        }

        if ($this->shouldSyncTournament($tournament)) {
            $this->setTournamentStatus($tournament, TournamentStatus::Sync);
        }

        if ($this->allTablesHaveFinished($tournament, $tableIds)) {
            $this->setTournamentStatus($tournament, TournamentStatus::Started);
        }
    }
}
