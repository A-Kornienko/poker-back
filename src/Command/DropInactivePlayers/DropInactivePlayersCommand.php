<?php

declare(strict_types=1);

namespace App\Command\DropInactivePlayers;

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
    name: 'dropInactivePlayers',
    description: 'drop inactive players',
)]
class DropInactivePlayersCommand extends Command implements CronCommandInterface
{
    public function __construct(
        protected KernelInterface $kernel,
        protected TableRepository $tableRepository,
    ) {
        parent::__construct();
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // bin/console dropInactivePlayers
        $io = new SymfonyStyle($input, $output);

        $io->title('drop inactive players process started');

        $this->drop();

        $io->title('drop inactive players finished');

        $io->success('drop inactive players finished successfully');

        return Command::SUCCESS;
    }

    public function drop(): void
    {
        try {
            $phpBinaryFinder = new PhpExecutableFinder();
            $phpBinaryPath = $phpBinaryFinder->find();
            $projectRoot = $this->kernel->getProjectDir();

            $process = new Process([
                $phpBinaryPath,
                $projectRoot . '/bin/console',
                'dropProcess'
            ]);
            $process->setOptions(['create_new_console' => true]);
            $process->start();
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/drop.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    public function isApplicable(): bool
    {
        return true;
    }
}
