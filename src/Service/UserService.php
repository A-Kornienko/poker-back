<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserRole;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use inc\User as MainUser;

class UserService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TableUserRepository $tableUserRepository
    ) {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function create(array $data): User
    {
        $user = (new User())
            ->setExternalId($data['externalId'])
            ->setEmail($data['email'])
            ->setLogin($data['login'])
            ->setPassword($data['pass'])
            ->setRole(UserRole::Player)
            ->setLastLogin(time());

        $this->save($user);

        return $user;
    }

    public function findMainUser(User $user)
    {
        return MainUser::findOneBy(['id' => $user->getExternalId()]);
    }

    public function getMyTables(User $user): array
    {
        $tableUsers = $this->tableUserRepository->findBy([
            'user' => $user, 
            'leaver' => false
        ]);

        $myTables = [];
        foreach ($tableUsers as $tableUser) {
            $myTables[] = $tableUser->getTable();
        }

        return $myTables;
    }
}
