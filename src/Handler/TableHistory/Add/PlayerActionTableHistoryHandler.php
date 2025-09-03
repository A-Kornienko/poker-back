<?php

declare(strict_types=1);

namespace App\Handler\TableHistory\Add;

use App\Entity\TableHistory;
use App\Enum\Round;
use App\Event\TableHistory\PlayerActionEvent;
use App\Repository\TableHistoryRepository;
use App\ValueObject\TableHistory\RoundActionTableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PlayerActionTableHistoryHandler implements AddTableHistoryHandlerInterface
{
    public function __construct(
        protected TableHistoryRepository $tableHistoryRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getRelatedEvent(): string
    {
        return PlayerActionEvent::NAME;
    }

    public function __invoke(Event $event): void
    {
        /** @var PlayerActionEvent $event */
        /** @var TableHistory $tableHistory */
        $tableHistory = $this->tableHistoryRepository->findOneBy(['session' => $event->getSession()]);
        $roundAction  = (new RoundActionTableHistory())
            ->setLogin($event->getLogin())
            ->setPlace($event->getPlace())
            ->setType($event->getActionType())
            ->setBetType($event->getBetType())
            ->setAmount($event->getAmount());

        match($event->getRound()) {
            Round::PreFlop => $tableHistory->addPreflop($roundAction),
            Round::Flop    => $tableHistory->addFlop($roundAction),
            Round::Turn    => $tableHistory->addTurn($roundAction),
            Round::River   => $tableHistory->addRiver($roundAction),
            default        => true
        };

        $this->entityManager->persist($tableHistory);
        $this->entityManager->flush();
    }
}
