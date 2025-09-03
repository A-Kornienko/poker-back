<?php

declare(strict_types=1);

namespace App\Handler\Balance;

use api\exchange\Currency;
use App\Entity\TableUser;
use App\Enum\TableUserInvoiceStatus;
use App\Handler\AbstractHandler;
use App\Repository\TableUserInvoiceRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReturnRemainingsPlayerBalanceHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableUserInvoiceRepository $tableUserInvoiceRepository,
        protected UserService $userService,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(TableUser $player)
    {
        if ($player->getTable()->getTournament()) {
            return;
        }

        $pendingInvoices = $this->tableUserInvoiceRepository->findBy([
            'table'  => $player->getTable(),
            'user'   => $player->getUser(),
            'status' => TableUserInvoiceStatus::Pending
        ]);

        $user         = $player->getUser();
        $mainUser     = $this->userService->findMainUser($user);

        if (!$mainUser) {
            return;
        }

        $convertedSum = Currency::converter($player->getStack(), 'USD', $mainUser->getUserInfo()['currency']);
        $mainUser->changeBalance(
            $convertedSum,
            'Poker: Return remaining balance from table ' . $player->getTable()->getName() . ' for sum ' . $convertedSum
        );

        $oldChipsQty = $player->getStack();
        $player->setStack(0);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            /** @var TableUserInvoice $invoice */
            foreach ($pendingInvoices as $invoice) {
                $invoice->setStatus(TableUserInvoiceStatus::Back);
                $this->entityManager->persist($invoice);
            }

            $this->entityManager->remove($player);
            $this->entityManager->flush();

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();
            $convertedSum = Currency::converter($oldChipsQty, 'USD', $mainUser->getUserInfo()['currency']);
            $mainUser->changeBalance(
                -$convertedSum,
                'Poker: Rollback of remaining balance return from table ' . $player->getTable()->getName() . ' for sum ' . $convertedSum
            );

            throw $e;
        }
    }
}
