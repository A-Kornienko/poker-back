<?php

declare(strict_types=1);

namespace App\Handler\Balance\Rebuy;

use App\Repository\TableUserInvoiceRepository;
use App\Service\TableUserInvoiceService;
use App\Entity\{Table, TableUser, User};
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReBuyAfterTournamentLoseHandler extends AbstractRebuyBalanceHandler
{
    public function __invoke(User $user, Table $table): void
    {
        $player = $this->tableUserRepository->findOneBy(['table' => $table, 'user' => $user]);

        $this->entityManager->getConnection()->beginTransaction();
        $amount = $player->getTable()->getTournament()->getSetting()->getBuyInSettings()->getChips();

        try {
            $this->validate($player, $amount);
            $this->addStack($player, $user, $amount);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    protected function validate(TableUser $player, float $amount): void
    {
        $this->validateTournamentBuyIn($player, $amount);
        $this->defaultLoseValidation($player, $amount);
    }
}
