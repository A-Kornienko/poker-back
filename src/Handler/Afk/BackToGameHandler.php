<?php

declare(strict_types=1);

namespace App\Handler\Afk;

use App\Entity\Table;
use App\Entity\TableUser;
use App\Entity\User;
use App\Repository\TableUserRepository;
use App\Handler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class BackToGameHandler extends AbstractHandler
{
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        protected TableUserRepository $tableUserRepository,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Table $table, User $user): void
    {
        /** @var TableUser $tableUser */
        $player = $this->tableUserRepository->findOneBy([
            'user' => $user,
            'table' => $table
        ]);

        $player->setSeatOut(null);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }
}

