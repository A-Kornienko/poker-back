<?php

declare(strict_types=1);

namespace App\Handler\TableHistory;

use App\Event\AbstractEvent;
use App\Handler\TableHistory\Add\AddTableHistoryHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class AddTableHistoryHandler
{
    protected iterable $addTableHistoryHandlers;

    public function __construct(
        #[TaggedIterator('app.tableHistory')]
        iterable $addTableHistoryHandlers,
    ) {
        $this->addTableHistoryHandlers = $addTableHistoryHandlers;
    }

    public function __invoke(AbstractEvent $event): void
    {
        /** @var AddTableHistoryHandlerInterface $addTableHistoryHandler */
        foreach ($this->addTableHistoryHandlers as $addTableHistoryHandler) {
            if ($addTableHistoryHandler->getRelatedEvent() === $event->getName()) {
                $addTableHistoryHandler($event);
            }
        }
    }
}
