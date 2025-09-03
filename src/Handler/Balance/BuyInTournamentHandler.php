<?php

declare(strict_types=1);

namespace App\Handler\Balance;

use api\exchange\Currency;
use App\Entity\{Tournament, User};
use App\Handler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuyInTournamentHandler extends AbstractHandler
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
        $mainUser->changeBalance(-$convertedSum, 'Poker: Registered on the tournament ' . $tournament->getName() . ' with sum ' . $tournament->getSetting()->getEntrySum());

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $rakeAmount = $tournament->getSetting()->getEntrySum() * $tournament->getSetting()->getRake();
            $tournament->setBalance($tournament->getBalance() + $tournament->getSetting()->getEntrySum() - $rakeAmount);
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();
            $mainUser->changeBalance($convertedSum, 'Poker: Rollback registration on the tournament ' . $tournament->getName());

            throw $e;
        }
    }
}
