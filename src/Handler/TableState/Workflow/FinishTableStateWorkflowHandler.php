<?php

declare(strict_types=1);

namespace App\Handler\TableState\Workflow;

use App\Entity\Table;
use App\Entity\TableUser;
use App\Enum\BankStatus;
use App\Event\TableHistory\FinishGameEvent;
use App\Handler\Balance\ApproveCacheTablePlayerInvoicesHandler;
use App\Handler\Balance\ApproveTournamentPlayerInvoicesHandler;
use App\Handler\Balance\ReturnRemainingsPlayerBalanceHandler;
use App\Handler\Bank\CalculateBankHandler;
use App\Handler\TimeBank\UpdateTimeBankHandler;
use App\Handler\Winner\DetectWinnerHandler;
use App\Repository\BankRepository;
use App\Repository\TableUserRepository;
use App\Repository\WinnerRepository;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FinishTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const ROUND_EXPIRATION_TIME = 10;

    protected const NAME = 'detectWinner';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected readonly TableUserRepository $tableUserRepository,
        protected WinnerRepository $winnerRepository,
        protected BankRepository $bankRepository,
        protected PlayerService $playerService,
        protected UpdateTimeBankHandler $timeBankHandler,
        protected ReturnRemainingsPlayerBalanceHandler $returnRemainingsPlayerBalanceHandler,
        protected ApproveCacheTablePlayerInvoicesHandler $approveCashTablePlayerInvoicesHandler,
        protected ApproveTournamentPlayerInvoicesHandler $approveTournamentPlayerInvoicesHandler,
        protected DetectWinnerHandler $detectWinnerHandler,
        protected CalculateBankHandler $calculateBankHandler,
        protected EventDispatcherInterface $dispatcher,
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Определяем победителей в раздаче, для столов любого типа.
     *
     * @param TransitionEvent $event
     * @throws Exception
     * @return void
     */
    public function runTransition(TransitionEvent $event): void
    {
        /** @var Table $table $table */
        $table = $event->getSubject();

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->playerService->preparePlayersToNewRound($table->getTableUsers()->toArray());

            ($this->calculateBankHandler)($table);
            ($this->detectWinnerHandler)($table);

            $this->dispatcher->dispatch(new FinishGameEvent($table), FinishGameEvent::NAME);

            /** @var TableUser $activePlayerSortedByPlace */
            $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);
            $this->playerService->setLoosePlayers($activePlayersSortedByPlace);
            $this->playerService->dropAfk($table);

            $table->setTurnPlace(0)->setRoundExpirationTime(time() + static::ROUND_EXPIRATION_TIME);

            $this->updateTableUsersBalance($table); // Обновляем баланс.
            $this->timeBankHandler->updateUserTimeBankByPeriod($table); // Обновляем timeBank по периоду

            $this->entityManager->persist($table);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw new Exception($e->getMessage());
        }
    }

    /**
     * Проверяем наличие победителей в бд, для текущей сессии стола.
     * Все банки в бд должны быть completed, для текущей сессии.
     *
     * @param EnterEvent $event
     *@throws Exception
     * @return void
     */
    public function runEnter(EnterEvent $event): void
    {
        /** @var Table $table $table */
        $table = $event->getSubject();

        $incompleteBanks = $this->bankRepository->count([
            'table'   => $table,
            'session' => $table->getSession(),
            'status'  => BankStatus::InProgress->value,
        ]);

        if ($incompleteBanks) {
            throw new Exception('Enter in FinishTableStateWorkflowHandler has blocked because some banks have incompleted status', 2000);
        }

        $winners = $this->winnerRepository->count([
            'table'   => $table,
            'session' => $table->getSession(),
        ]);
        if (!$winners) {
            throw new Exception('Enter in FinishTableStateWorkflowHandler has blocked because winners have not been resolved', 2000);
        }
    }

    protected function updateTableUsersBalance(Table $table)
    {
        foreach ($table->getTableUsers()->toArray() as $player) {
            if ($player->getLeaver()) {
                ($this->returnRemainingsPlayerBalanceHandler)($player);
                continue;
            }
            if ($table->getTournament()) {
                ($this->approveTournamentPlayerInvoicesHandler)($player);
            } else {
                ($this->approveCashTablePlayerInvoicesHandler)($player);
            }
        }
    }
}
