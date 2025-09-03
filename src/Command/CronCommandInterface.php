<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.cronCommands')]
interface CronCommandInterface
{
    public function isApplicable(): bool;

    public function execute(InputInterface $input, OutputInterface $output): int;
}
