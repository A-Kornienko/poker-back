<?php

namespace App\Handler\TableState\Workflow;

use App\Entity\Table;
use App\Enum\ReformTableQueueStatus;
use App\Handler\Tournaments\ReformTablesHandler;
use App\Repository\ReformTableQueueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class InitTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const NAME = 'seatTables';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ReformTableQueueRepository $reformTableQueueRepository,
        protected ReformTablesHandler $reformTablesHandler,
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
            $reformTableQueues = $this->reformTableQueueRepository->findBy([
                'tournament' => $table->getTournament(),
                'status'     => [ReformTableQueueStatus::Pending, ReformTableQueueStatus::Process]
            ]);

            if (!$reformTableQueues) {
                return;
            }

            foreach ($reformTableQueues as $reformTableQueue) {
                ($this->reformTablesHandler)($reformTableQueue);
            }
        }
    }

    public function runEnter(EnterEvent $event): void
    {
        /** @var Table $table */
        $table = $event->getSubject();

        if (!$table->getTournament()) {
            return;
        }

        $reformTableQueues = $this->reformTableQueueRepository->findBy([
            'tournament' => $table->getTournament(),
            'status'     => ReformTableQueueStatus::Pending,
        ]);

        if ($reformTableQueues) {
            throw new \Exception('Init table state in process, because reform tables is not finished', 2000);
        }
    }
}
