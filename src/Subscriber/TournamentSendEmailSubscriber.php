<?php

namespace App\Subscriber;

use api\email\MailController;
use App\Event\Tournament\Email\CancelEvent;
use App\Event\Tournament\Email\EndEvent;
use App\Event\Tournament\Email\PlayerRankTournamentEvent;
use App\Event\Tournament\Email\RegisterEvent;
use App\Event\Tournament\Email\StartEvent;
use App\Event\Tournament\Email\UnregisterEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TournamentSendEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected MailController $mailController
    ) {
    }

    public function executeEvent(Event $event): void
    {
        $event->execute($this);
    }

    public function sendEmail(array $data): void
    {
        $this->mailController->sendMail(
            to: $data['to'],
            subject: $data['subject'],
            mailBody: $data['message'],
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StartEvent::NAME                => 'executeEvent',
            EndEvent::NAME                  => 'executeEvent',
            RegisterEvent::NAME             => 'executeEvent',
            UnregisterEvent::NAME           => 'executeEvent',
            PlayerRankTournamentEvent::NAME => 'executeEvent',
            CancelEvent::NAME               => 'executeEvent',
        ];
    }
}
