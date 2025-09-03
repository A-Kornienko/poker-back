<?php

declare(strict_types=1);

namespace App\Command\AutoGame;

use App\Handler\TableState\TableStateProcessHandler;
use App\Repository\TableRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'tableStateProcess',
    description: 'tableStateProcess',
)]
class TableStateProcessCommand extends Command
{
    public function __construct(
        protected TableRepository $tableRepository,
        protected TableStateProcessHandler $tableStateProcessHandler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('offset', InputArgument::OPTIONAL, 'offset', 4);
        $this->addArgument('limit', InputArgument::OPTIONAL, 'limit', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit  = (int) $input->getArgument('limit');
        $offset = (int) $input->getArgument('offset');

        $startTime = time() + 60;

        ($this->tableStateProcessHandler)($offset, $limit, $startTime);

        return Command::SUCCESS;
    }
}
