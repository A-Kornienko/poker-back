<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Handler\TableState\Workflow\TableStateWorkflowHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class TableStateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[TaggedIterator('app.workflow')]
        protected iterable $tableStateHandlers,
    ) {
    }

    public function runTransition(TransitionEvent $event): void
    {
        $transitionName = $event->getTransition()->getName();

        /** @var TableStateWorkflowHandlerInterface $workflow */
        foreach ($this->tableStateHandlers as $tableStateHandler) {
            if ($tableStateHandler->getName() === $transitionName) {
                $tableStateHandler->runTransition($event);
            }
        }
    }

    public function runEnter(EnterEvent $event): void
    {
        $transitionName = $event->getTransition()->getName();

        /** @var TableStateWorkflowHandlerInterface $workflow */
        foreach ($this->tableStateHandlers as $tableStateHandler) {
            if ($tableStateHandler->getName() === $transitionName) {
                $tableStateHandler->runEnter($event);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.table_state.enter'      => ['runEnter'],
            'workflow.table_state.transition' => ['runTransition'],
        ];
    }
}
