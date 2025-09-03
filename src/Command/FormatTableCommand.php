<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ReformTableQueue;
use App\Entity\Table;
use App\Enum\TournamentStatus;
use App\Repository\TournamentRepository;
use App\Service\TableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'formatTables',
    description: 'Format tables',
)]
class FormatTableCommand extends Command implements CronCommandInterface
{
    public function __construct(
        protected TableService $tableService,
        protected TournamentRepository $tournamentRepository,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $tournaments = $this->tournamentRepository->findBy([
            'status' => [TournamentStatus::Started, TournamentStatus::Break, TournamentStatus::Sync]
        ]);

        foreach ($tournaments as $tournament) {
            $formatTables = $tournament->getTables()->filter(
                fn(Table $table) => !$table->getIsArchived()
            );

            if ($formatTables->count() < 2) {
                continue;
            }

            $formatTables = $formatTables->filter(
                fn(Table $table) => $table->getTableUsers()->count() < 5
            );

            if ($formatTables->count() < 1) {
                continue;
            }

            foreach ($formatTables as $formatTable) {
                $formatTable = $formatTable[0];

                $reformTableQueue = $this->tableService->getReformTableQueue($formatTable);

                if (
                    !$reformTableQueue
                    && $formatTable->getTournament()->getStatus() !== TournamentStatus::Finished
                    && $formatTable->getTournament()->getStatus() !== TournamentStatus::Canceled
                ) {
                    // Создаем новый экземпляр сущности
                    $reformTableQueue = new ReformTableQueue();
                    $reformTableQueue->setTable($formatTable);
                    $reformTableQueue->setTournament($formatTable->getTournament());
                    $reformTableQueue->setTableSession($formatTable->getSession() ?? '');

                    // Сохраняем сущность в базе данных
                    $this->entityManager->persist($reformTableQueue);
                    $this->entityManager->flush();
                }
            }
        }

        $output->writeln('Success add new reformTableQueue');

        return Command::SUCCESS;
    }

    public function isApplicable(): bool
    {
        return true;
    }
}
