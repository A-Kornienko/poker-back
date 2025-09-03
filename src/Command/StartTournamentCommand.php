<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Tournament;
use App\Handler\Tournaments\StartTournamentHandler;
use App\Repository\TournamentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'startTournaments',
    description: 'Start tournaments',
)]
class StartTournamentCommand extends Command implements CronCommandInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TournamentRepository $tournamentRepository,
        protected StartTournamentHandler $tournamentStartHandler,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // php bin/console startTournaments
        try {
            $tournaments = $this->tournamentRepository->getPendingTournaments();

            if (!$tournaments) {
                $output->writeln('No tournaments found');

                return Command::SUCCESS;
            }

            /** @var Tournament $tournament */
            foreach ($tournaments as $tournament) {
                ($this->tournamentStartHandler)($tournament);
            }

            $output->writeln('Executing command');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            file_put_contents(__DIR__ . '/log.txt', (new DateTime())->format('Y:m:d H:i:s') . ': ' . $e->getMessage() . ': ' . $e->getTraceAsString(), FILE_APPEND);

            return Command::FAILURE;
        }
    }

    public function isApplicable(): bool
    {
        return true; // возвращает всегда true
    }
}
