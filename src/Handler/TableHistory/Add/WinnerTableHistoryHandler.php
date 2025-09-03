<?php

declare(strict_types=1);

namespace App\Handler\TableHistory\Add;

use App\Event\TableHistory\WinnerEvent;
use App\Repository\TableHistoryRepository;
use App\ValueObject\TableHistory\WinnerTableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class WinnerTableHistoryHandler implements AddTableHistoryHandlerInterface
{
    public function __construct(
        protected TableHistoryRepository $tableHistoryRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getRelatedEvent(): string
    {
        return WinnerEvent::NAME;
    }

    public function __invoke(Event $event): void
    {
        /** @var WinnerEvent $event */
        $winners = $event->getWinners();

        if (count($winners) < 1) {
            return;
        }

        $winners[0]->getSession();

        $tableHistory = $this->tableHistoryRepository->findOneBy([
            'table'   => $winners[0]->getTable(),
            'session' => $winners[0]->getSession()
        ]);

        if (!$tableHistory) {
            return;
        }

        foreach ($winners as $winner) {
            $tableHistory->addWinner((new WinnerTableHistory())->fromArray([
                'login'       => $winner->getUser()->getLogin(),
                'combination' => $winner->getTableUser()->getCombination(),
                'handCards'   => $winner->getTableUser()->getCards(true),
                'sum'         => $winner->getSum(),
            ]));
        }

        $this->entityManager->persist($tableHistory);
        $this->entityManager->flush();
    }
}
