<?php

declare(strict_types=1);

namespace App\Handler\TimeBank;

use App\Entity\Table;
use App\Entity\TableUser;
use App\Entity\User;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActivateTimeBankHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TableUserRepository $tableUserRepository,
        protected UpdateTimeBankHandler $updateTimeBankHandler
    ) {
    }

    public function __invoke(Table $table, User $user): array
    {
        /** @var TableUser $tableUser */
        $tableUser = $this->tableUserRepository->findOneBy([
            'user'  => $user,
            'table' => $table
        ]);

        if ($tableUser->getTimeBank()->isActive()) {
            return ['time' => $tableUser->getBetExpirationTime()];
        }

        $timeBank = $this->updateTimeBankHandler->updateUserTimeBank($tableUser->getTimeBank(), $table);

        $tableUser->setBetExpirationTime($tableUser->getBetExpirationTime() + $timeBank->getTime());
        $tableUser->setTimeBank($timeBank);
        $this->entityManager->persist($tableUser);
        $this->entityManager->flush();

        return ['time' => $tableUser->getBetExpirationTime()];
    }
}
