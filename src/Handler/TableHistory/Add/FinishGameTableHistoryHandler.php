<?php

namespace App\Handler\TableHistory\Add;

use App\Enum\TableUserStatus;
use App\Event\TableHistory\FinishGameEvent;
use App\Event\TableHistory\PlayerEvent;
use App\Repository\TableHistoryRepository;
use App\ValueObject\TableHistory\PlayerTableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FinishGameTableHistoryHandler implements AddTableHistoryHandlerInterface
{
    public function __construct(
        protected TableHistoryRepository $tableHistoryRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getRelatedEvent(): string
    {
        return FinishGameEvent::NAME;
    }

    public function __invoke(Event $event): void
    {
        /** @var PlayerEvent $event */
        $table        = $event->getTable();
        $tableHistory = $this->tableHistoryRepository->findOneBy([
            'table'   => $table,
            'session' => $table->getSession()
        ]);

        if (!$tableHistory) {
            return;
        }

        $players = [];
        foreach ($table->getTableUsers() as $player) {
            $players[] = (new PlayerTableHistory())->fromArray([
                'place' => $player->getPlace(),
                'login' => $player->getUser()->getLogin(),
                'cards' => $player->getStatus()->value === TableUserStatus::Active->value ? $player->getCards(true) : [],
                'stack' => $player->getStack(),
            ]);
        }

        $tableHistory->setCards(...$table->getCards());
        $tableHistory->setPlayers($players);

        $this->entityManager->persist($tableHistory);
        $this->entityManager->flush();
    }
}
