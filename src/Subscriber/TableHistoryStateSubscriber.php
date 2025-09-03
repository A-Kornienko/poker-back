<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Event\TableHistory\FinishGameEvent;
use App\Event\TableHistory\PlayerActionEvent;
use App\Event\TableHistory\PotEvent;
use App\Event\TableHistory\StartGameEvent;
use App\Event\TableHistory\WinnerEvent;
use App\Handler\TableHistory\AddTableHistoryHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TableHistoryStateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected AddTableHistoryHandler $addTableHistoryHandler
    ) {
    }

    public function executeEvent(Event $event): void
    {
        ($this->addTableHistoryHandler)($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StartGameEvent::NAME    => 'executeEvent',
            FinishGameEvent::NAME   => 'executeEvent',
            PlayerActionEvent::NAME => 'executeEvent',
            PotEvent::NAME          => 'executeEvent',
            WinnerEvent::NAME       => 'executeEvent',
        ];
    }
}
