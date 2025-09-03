<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Throwable;

#[AsCommand(
    name: 'CronCommand',
    description: 'Main cron command',
)]
class CronCommand extends Command
{
    protected iterable $cronCommands;

    public function __construct(
        #[TaggedIterator('app.cronCommands')]
        iterable $cronCommands
    ) {
        parent::__construct();
        $this->cronCommands = $cronCommands;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $currentTime = time();
            $output->writeln('Current time: ' . date('Y-m-d H:i:s', $currentTime));

            foreach ($this->cronCommands as $command) {
                if ($command->isApplicable()) {
                    $command->execute($input, $output);
                }
            }
        } catch(Throwable $e) {
            file_put_contents(__DIR__ . '/log.txt', $e->getTraceAsString() . ', Message: ' . $e->getMessage(), FILE_APPEND);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
