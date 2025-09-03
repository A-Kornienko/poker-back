<?php

declare(strict_types=1);

namespace App\Command\AutoGame;

use App\Command\CronCommandInterface;
use App\Repository\TableRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'autoGame',
    description: 'auto game',
)]
class AutoGameCommand extends Command implements CronCommandInterface
{
    public function __construct(
        protected KernelInterface $kernel,
        protected TableRepository $tableRepository,
    ) {
        parent::__construct();
    }

    public function isApplicable(): bool
    {
        return true;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // bin/console autoGame
        $io = new SymfonyStyle($input, $output);

        $io->title('autoGame started');

        $this->autoGame();

        $io->title('autoGame finished');

        $io->success('autoGame finished successfully');

        return Command::SUCCESS;
    }

    public function autoGame(): void
    {
        try {
            $phpBinaryFinder = new PhpExecutableFinder();
            $phpBinaryPath   = $phpBinaryFinder->find();
            $projectRoot     = $this->kernel->getProjectDir();

            $limit       = 25;
            $totalTables = $this->tableRepository->count(['isArchived' => false]);

            for ($offset = 0; $offset < $totalTables; $offset += $limit) {
                $process = new Process([
                    $phpBinaryPath,
                    $projectRoot . '/bin/console', 'tableStateProcess', $offset, $limit
                ]);
                $process->setOptions(['create_new_console' => true]);
                $process->start();
            }
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/stress.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    public static function getDefaultPriority(): int
    {
        return 1;
    }
}
