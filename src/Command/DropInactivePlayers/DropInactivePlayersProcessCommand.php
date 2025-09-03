<?php

declare(strict_types=1);

namespace App\Command\DropInactivePlayers;

use App\Handler\TableState\DropInactivePlayersHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'dropProcess',
    description: 'dropProcess',
)]
class DropInactivePlayersProcessCommand extends Command
{
    public function __construct(
       protected DropInactivePlayersHandler $dropInactivePlayersHandler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = time() + 60;

        ($this->dropInactivePlayersHandler)($startTime);

    return Command::SUCCESS;
    }
}
