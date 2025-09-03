<?php

declare(strict_types=1);

namespace App\Handler\TableState\Workflow;

use App\Entity\TableUser;
use App\Enum\Round;
use App\Enum\TableUserStatus;
use App\Event\TableHistory\StartGameEvent;
use App\Handler\Blinds\SetBigBlindHandler;
use App\Handler\Blinds\SetSmallBlindHandler;
use App\Handler\Cards\SetPlayerCardsHandler;
use App\Handler\Bet\AutoBlindHandler;
use App\Handler\TableState\TurnHandler;
use App\Handler\TimeBank\UpdateTimeBankHandler;
use App\Repository\TableHistoryRepository;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StartTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const NAME = 'startGame';

    public function __construct(
        protected readonly TableUserRepository $tableUserRepository,
        protected UpdateTimeBankHandler $timeBankHandler,
        protected readonly TurnHandler $turnHandler,
        protected SetSmallBlindHandler $setSmallBlindHandler,
        protected SetBigBlindHandler $setBigBlindHandler,
        protected readonly SetPlayerCardsHandler $setPlayerCardsHandler,
        protected AutoBlindHandler $autoBlindHandler,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function runEnter(EnterEvent $event): void
    {
        $table = $event->getSubject();

        if (!$table->getTurnPlace()) {
            throw new Exception('The turn place is not set yet', 2000);
        }

        foreach ($table->getTableUsers()->filter(fn(TableUser $player) => $player->getStatus()->value === TableUserStatus::Active->value) as $tableUser) {
            if (!$tableUser->getCards()) {
                throw new Exception('Cards for users are not set yet', 2000);
            }
        }

        if (!$table->getSession()) {
            throw new Exception('The session is not started yet', 2000);
        }
    }

    public function runTransition(TransitionEvent $event): void
    {
        $table = $event->getSubject();
        ($this->autoBlindHandler)($table);

        $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);

        $this->timeBankHandler->updateTimeBankByHandCount($table, $activePlayersSortedByPlace);

        $table->setSession(Uuid::v4()->jsonSerialize());
        $table->setRound(Round::PreFlop);

        // Устанавливаем диллера, блайнды за столом и ход первого игрока.
        $table = $this->turnHandler->setDealer($table, $activePlayersSortedByPlace);
        $table = ($this->setSmallBlindHandler)($table, $activePlayersSortedByPlace);
        $activeWaitingPlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table, [
            TableUserStatus::Active->value,
            TableUserStatus::WaitingBB->value,
        ]);
        $table = ($this->setBigBlindHandler)($table, $activeWaitingPlayersSortedByPlace);

        $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);
        $table = $this->turnHandler->setFirstTurn($table, $activePlayersSortedByPlace);
        ($this->setPlayerCardsHandler)($activePlayersSortedByPlace, $table->getSetting()->getRule()->countPlayerCards());

        $this->dispatcher->dispatch(new StartGameEvent($table), StartGameEvent::NAME);
    }
}
