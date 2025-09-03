<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Table;
use App\Entity\Tournament;
use App\Handler\AbstractHandler;
use App\Service\TableService;
use App\Service\TableSettingService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GenerateTournamentTableHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableService $tableService,
        protected EntityManagerInterface $entityManager,
        protected TableSettingService $tableSettingService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Tournament $tournament): Collection
    {
        $countNecessaryTournamentTables = $this->tableService->getCountTablesForTournament($tournament);

        if ($tournament->getTables()->count() >= $countNecessaryTournamentTables) {
            return $tournament->getTables();
        }

        $existedTables = $tournament->getTables()->filter(
            fn($table) => !$table->getIsArchived()
        );

        if ($existedTables->count() >= $countNecessaryTournamentTables) {
            return $existedTables;
        }

        $countNecessaryTournamentTables -= $tournament->getTables()->count();

        $tableSetting = $this->tableSettingService->createByTournament($tournament);

        for ($i = 0; $i < $countNecessaryTournamentTables; $i++) {
            $table = new Table();
            $table
                ->setName($tournament->getName() . ' Table ' . ($i + 1))
                ->setSmallBlind($tableSetting->getSmallBlind())
                ->setBigBlind($tableSetting->getBigBlind())
                ->setSetting($tableSetting)
                ->setTournament($tournament);

            $tournament->getTables()->add($table);
            $this->entityManager->persist($table);
        }

        $this->entityManager->flush();

        return $tournament->getTables()->filter(
            fn($table) => !$table->getIsArchived()
        );
    }
}
