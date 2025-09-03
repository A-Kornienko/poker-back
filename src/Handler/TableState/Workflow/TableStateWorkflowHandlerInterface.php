<?php

declare(strict_types=1);

namespace App\Handler\TableState\Workflow;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.workflow')]
interface TableStateWorkflowHandlerInterface
{
    public function getName(): string;
}
