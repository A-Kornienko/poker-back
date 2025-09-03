<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Workflow\WorkflowInterface;

class UpdateTableStateHandler
{
    public function __construct(
        protected WorkflowInterface $tableStateStateMachine,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(Table $table): void
    {
        $transitions = $this->tableStateStateMachine->getEnabledTransitions($table);
        foreach ($transitions as $enabledTransition) {
            if ($this->tableStateStateMachine->can($table, $enabledTransition->getName())) {
                $this->tableStateStateMachine->apply($table, $enabledTransition->getName());
            }

            $this->entityManager->persist($table);
            $this->entityManager->flush();

            break;
        }
    }
}
