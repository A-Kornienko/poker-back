<?php

declare(strict_types=1);

namespace App\Handler\TableState\Workflow;

use App\Entity\{TableUser};
use App\Enum\{Round};
use App\Handler\Bet\Strategy\{CallHandler, CheckHandler, FoldHandler};
use App\Handler\Blinds\UpdateTournamentBlindsHandler;
use App\Handler\TableState\RoundHandler;
use App\Handler\TimeBank\UpdateTimeBankHandler;
use App\Repository\TableUserRepository;
use App\Service\{PlayerService};
use Exception;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class RunTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const NAME = 'runRound';

    public function __construct(
        protected readonly TableUserRepository $tableUserRepository,
        protected readonly CheckHandler  $checkHandler,
        protected readonly FoldHandler   $foldHandler,
        protected readonly CallHandler   $callHandler,
        protected UpdateTournamentBlindsHandler $updateTournamentBlindsHandler,
        protected UpdateTimeBankHandler  $timeBankHandler,
        protected PlayerService          $playerService,
        protected RoundHandler           $roundHandler
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function runEnter(EnterEvent $event): void
    {
        $table = $event->getSubject();
        if (
            $table->getRound()->value !== Round::FastFinish->value
            && $table->getRound()->value !== Round::ShowDown->value
        ) {
            throw new Exception('Round ' . $table->getRound()->value . ' in progress', 2000);
        }
    }

    public function runTransition(TransitionEvent $event): void
    {
        $table = $event->getSubject();

        ($this->updateTournamentBlindsHandler)($table);
        $this->timeBankHandler->updateUserTimeBankByPeriod($table);

        $activePlayers = $this->tableUserRepository->getPlayersSortedByPlace($table);

        // Если все игроки кроме последнего играют в пас, запускаем быстрый финиш раздачи
        if ($this->roundHandler->isFastFinishRoundStarted($table, $activePlayers)) {
            return;
        }

        $maxBet        = $this->tableUserRepository->getMaxBet($table);
        $activePlayers = $this->playerService->excludeSilentPlayers($activePlayers);

        if ($this->roundHandler->isNextRoundStarted($table, $activePlayers, $maxBet)) {
            return;
        }

        /** @var TableUser $currentPlayer */
        $currentPlayer = $activePlayers[$table->getTurnPlace()];
        if ($currentPlayer->getSeatOut()) {
            $this->foldHandler->makeBet($currentPlayer);
            return;
        }

        if ($currentPlayer->getBetExpirationTime() < time()) {
            match (true) {
                $maxBet > $currentPlayer->getBet() => $this->foldHandler->makeBet($currentPlayer, auto: true),
                default                            => $this->checkHandler->makeBet($currentPlayer, auto: true),
            };
        }
    }
}
