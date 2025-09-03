<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{TableUser, TableUserInvoice};
use App\Enum\TableUserInvoiceStatus;
use App\Handler\Balance\{
    ApproveCacheTablePlayerInvoicesHandler,
    ApproveTournamentPlayerInvoicesHandler,
    ReturnRemainingsPlayerBalanceHandler};
use App\Repository\TableUserInvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TableUserInvoiceService
{
    public function __construct(
        protected TableUserInvoiceRepository $tableUserInvoiceRepository,
        protected UserService $userService,
        protected ReturnRemainingsPlayerBalanceHandler $returnRemainingsPlayerBalanceHandler,
        protected ApproveCacheTablePlayerInvoicesHandler $approveCashTablePlayerInvoicesHandler,
        protected ApproveTournamentPlayerInvoicesHandler $approveTournamentPlayerInvoicesHandler,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
    ) {
    }

    public function create(TableUser $player, float $chips): void
    {
        $userInvoice = new TableUserInvoice();
        $userInvoice->setTable($player->getTable());
        $userInvoice->setUser($player->getUser());
        $userInvoice->setSum($chips);
        $userInvoice->setStatus(TableUserInvoiceStatus::Pending);
        $userInvoice->setUpdatedAt(time());

        $this->entityManager->persist($userInvoice);
    }
}
