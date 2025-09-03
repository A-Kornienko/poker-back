<?php

declare(strict_types=1);

namespace App\Handler\Balance;

use api\exchange\Currency;
use App\Entity\{Tournament, User};
use App\Handler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class RefundTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Tournament $tournament, User $user): void
    {
        $mainUser     = $user->getMainUser();

        if (!$mainUser) {
            return;
        }

        $convertedSum = Currency::converter($tournament->getSetting()->getEntrySum(), 'USD', $mainUser->getUserInfo()['currency']);
        $mainUser->changeBalance($convertedSum, 'Poker: Canceled registration on the tournament ' . $tournament->getName());

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $rakeAmount = $tournament->getSetting()->getEntrySum() * $tournament->getSetting()->getRake();
            $tournament->setBalance($tournament->getBalance() - ($tournament->getSetting()->getEntrySum() - $rakeAmount));

            $this->entityManager->persist($tournament);
            $this->entityManager->persist($user);
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }
}
