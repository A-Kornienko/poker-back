<?php

declare(strict_types=1);

namespace App\Handler\Balance;

use api\exchange\Currency;
use App\Entity\TableUser;
use App\Enum\{TableUserInvoiceStatus, TableUserStatus};
use App\Handler\AbstractHandler;
use App\Helper\Calculator;
use App\Repository\TableUserInvoiceRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApproveTournamentPlayerInvoicesHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableUserInvoiceRepository $tableUserInvoiceRepository,
        protected UserService $userService,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(TableUser $player): void
    {
        $pendingInvoices = $this->tableUserInvoiceRepository->findBy([
            'table'  => $player->getTable(),
            'user'   => $player->getUser(),
            'status' => TableUserInvoiceStatus::Pending
        ]);

        $user          = $player->getUser();
        $mainUser      = $this->userService->findMainUser($user);
        $tournamentSum = $player->getTable()->getTournament()->getSetting()->getBuyInSettings()->getSum();

        if (!$pendingInvoices) {
            return;
        }

        $this->entityManager->getConnection()->beginTransaction();
        $handledInvoices = [];

        try {
            /** @var TableUserInvoice $invoice */
            foreach ($pendingInvoices as $invoice) {
                $chips        = Calculator::add($invoice->getSum(), $player->getStack());
                $convertedSum = Currency::converter($tournamentSum, 'USD', $mainUser->getUserInfo()['currency']);

                if ($mainUser->getBalance() >= $convertedSum) {
                    $mainUser->changeBalance(
                        -$convertedSum,
                        'Poker:  Rebuy ' . $chips . ' chips on the tournament' . $player->getTable()->getTournament()->getName() . ' for sum ' . $convertedSum
                    );

                    $player->setStack($chips);
                    $player->setCountByuIn($player->getCountByuIn() + 1);
                    $invoice->setStatus(TableUserInvoiceStatus::Completed);

                    $this->entityManager->persist($invoice);
                    continue;
                }

                $invoice->setStatus(TableUserInvoiceStatus::Failed);
                $this->entityManager->persist($invoice);

                $handledInvoices[] = $invoice;
            }

            $player->setStatus(TableUserStatus::Active);
            $this->entityManager->persist($player);
            $this->entityManager->flush();

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();

            foreach ($handledInvoices as $invoice) {
                $convertedSum = Currency::converter($invoice->getSum(), 'USD', $mainUser->getUserInfo()['currency']);
                $mainUser->changeBalance(
                    $convertedSum,
                    'Poker: Rollback rebuy ' . $chips . ' chips on the tournament ' . $player->getTable()->getTournament()->getName() . ' for sum ' . $convertedSum
                );
            }

            throw $e;
        }
    }
}
