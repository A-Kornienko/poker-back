<?php

declare(strict_types=1);

namespace App\Handler\Balance\Rebuy;

use api\exchange\Currency;
use App\Entity\Table;
use App\Entity\TableUser;
use App\Entity\User;
use App\Enum\TableUserInvoiceStatus;
use App\Enum\TableUserStatus;
use App\Exception\ResponseException;
use App\Helper\Calculator;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserInvoiceRepository;
use App\Repository\TableUserRepository;
use App\Service\TableUserInvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractRebuyBalanceHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected TableUserInvoiceRepository $tableUserInvoiceRepository,
        protected TableUserInvoiceService $tableUserInvoiceService,
        protected TableUserRepository $tableUserRepository,
    ) {}

    protected function defaultLoseValidation(TableUser $player, float $amount): void
    {
        match (true) {
            $player->getStack() > 0
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::BIG_BALANCE_FOR_BUY_IN),
            $amount < $player->getTable()->getSetting()->getBigBlind() * 20 && $amount > $player->getTable()->getSetting()->getBigBlind() * 500
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::INCORRECT_BALANCE),
            $player->getStatus() !== TableUserStatus::Lose => ErrorCodeHelper::YOU_ARE_NOT_A_LOSER,
            default => true
        };
    }

    protected function validateTournamentBuyIn(TableUser $player, float $amount): void
    {
        $tournament = $player->getTable()->getTournament();

        match (true) {
            $tournament->getSetting()->getBuyInSettings()->getSum() < $amount
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_AMOUNT_BUY_IN_BIG),
            $tournament->getSetting()->getBuyInSettings()->getSum() > $amount
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_AMOUNT_BUY_IN_SMALL),
            $tournament->getDateStart() + $tournament->getSetting()->getBuyInSettings()->getLimitByTime() < time()
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_BUY_IN_LIMIT_TIME),
            $tournament->getTournamentUsers()->count() <= $tournament->getSetting()->getBuyInSettings()->getLimitByCountPlayers()
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_BUY_IN_LIMIT_COUNT_PLAYERS),
            $tournament->getSetting()->getEntryChips() * $tournament->getSetting()->getBuyInSettings()->getLimitByChipsInPercent() / 100 < $player->getStack()
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_BUY_IN_TOO_MANY_CHIPS),
            $tournament->getSetting()->getBuyInSettings()->getLimitByNumberOfTimes() <= $player->getCountByuIn()
            => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_BUY_IN_LIMIT_BY_NUMBER_TIMES),

            default => true
        };
    }

    protected function getTableUser(User $user, Table $table): TableUser
    {
        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user,
            'table' => $table
        ]);

        if (!$player) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::PLAYER_NOT_FOUND);
        }

        return $player;
    }

    protected function defaultBuyIn(Table $table, User $user, float $stack)
    {
        $player = $this->tableUserRepository->findOneBy(['table' => $table, 'user' => $user]);

        if (!$player) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::PLAYER_NOT_FOUND);
        }

        $mainUser           = $user->getMainUser();

        if (!$mainUser) {
            return;
        }

        $actualUsdBalance   = Currency::converter($mainUser->getBalance(), $mainUser->getUserInfo()['currency'], 'USD');
        $sumPendingInvoices = 0;

        $pendingInvoices = $this->tableUserInvoiceRepository->findBy([
            'table'  => $player->getTable(),
            'user'   => $player->getUser(),
            'status' => TableUserInvoiceStatus::Pending
        ]);

        foreach ($pendingInvoices as $pendingInvoice) {
            $sumPendingInvoices = Calculator::add($sumPendingInvoices, $pendingInvoice->getSum());
        }

        if ($actualUsdBalance < Calculator::add($sumPendingInvoices, $stack)) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::INCORRECT_BALANCE);
        }

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->tableUserInvoiceService->create($player, $stack);
            $this->entityManager->flush(); // Сохраняем изменения в базе данных
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }

    protected function addStack(TableUser $player, User $user, float $amount): void
    {
        $mainUser = $user->getMainUser();
        if (!$mainUser) {
            return;
        }
        $chips = $player->getStack();
        $convertedSum = Currency::converter($amount, 'USD', $mainUser->getUserInfo()['currency']);

        if ($mainUser->getBalance() >= $convertedSum) {
            $mainUser->changeBalance(
                -$convertedSum,
                'Poker:  Rebuy ' . $chips . ' chips at the cash table' . $player->getTable()->getName(
                ) . ' for sum ' . $convertedSum
            );

            $player->setStack($chips + $amount);
            $player->setStatus(TableUserStatus::Pending);

            $this->entityManager->persist($player);
        }
    }
}
