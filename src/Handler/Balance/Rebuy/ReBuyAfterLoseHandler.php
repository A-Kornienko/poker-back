<?php

declare(strict_types=1);

namespace App\Handler\Balance\Rebuy;

use App\Entity\{Table, User};

class ReBuyAfterLoseHandler extends AbstractRebuyBalanceHandler
{
    public function __invoke(User $user, Table $table, float $amount): void
    {
        $player = $this->tableUserRepository->findOneBy(['table' => $table, 'user' => $user]);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->defaultLoseValidation($player, $amount);
            $this->addStack($player, $user, $amount);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }
}
