<?php

declare(strict_types=1);

namespace App\Handler\Bet;

use App\Entity\Table;
use App\Enum\TableUserStatus;
use App\Handler\AbstractHandler;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SetAutoBlindStatusHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableUserRepository $tableUserRepository,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Table $table): void
    {
        $user   = $this->security->getUser();
        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user->getId(),
            'table' => $table
        ]);

        $player->setStatus(TableUserStatus::AutoBlind);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }
}
