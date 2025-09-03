<?php

namespace App\Handler\TableState\Workflow;

use App\Entity\Table;
use App\Enum\TournamentStatus;
use App\Handler\Tournaments\SynchronizationTournamentTablesHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class SyncTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const NAME = 'syncTables';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SynchronizationTournamentTablesHandler $synchronizationTournamentTablesHandler
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function runTransition(TransitionEvent $event): void
    {
        /** @var Table $table */
        $table = $event->getSubject();

        if ($table->getTournament()) {
            ($this->synchronizationTournamentTablesHandler)($table->getTournament());
        }
    }

    public function runEnter(EnterEvent $event): void
    {
        /** @var Table $table */
        $table = $event->getSubject();

        if (!$table->getTournament() && !$table->getTournament()?->getSetting()->getTableSynchronization()) {
            return;
        }

        if ($table->getTournament()->getStatus() === TournamentStatus::Sync) {
            throw new \Exception('Synchronization tables in progress', 2000);
        }
    }
}
