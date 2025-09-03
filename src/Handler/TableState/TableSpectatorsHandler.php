<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Entity\Table;
use App\Entity\TableSpectator;
use App\Entity\User;
use App\Handler\AbstractHandler;
use App\Repository\SpectatorRepository;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableSpectatorsHandler extends AbstractHandler
{
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected SpectatorRepository $spectatorRepository,
        protected TableUserRepository $tableUserRepository,
    ) {
        parent::__construct($security, $translator);
    }

    public function addSpectator(Request $request, Table $table, ?User $user): array
    {
        if ($user) {
            $tableUser = $this->tableUserRepository->findOneBy([
                'table' => $table,
                'user'  => $user,
            ]);

            if ($tableUser) {
                return [];
            }
        }

        $uuid = $request->cookies->get('spectator_uuid', Uuid::v4()->jsonSerialize());

        $tableSpectator = $this->spectatorRepository->findOneBy([
            'table' => $table,
            'uuid'  => $uuid
        ]);

        if (!$tableSpectator) {
            $tableSpectator = (new TableSpectator())
                ->setTable($table)
                ->setUuId($uuid);
        }

        $tableSpectator->setExpirationTime(time() + 5);

        $this->entityManager->persist($tableSpectator);
        $this->entityManager->flush();

        return ['Set-Cookie' => "spectator_uuid=$uuid; Path=/; Max-Age=" . (time() + 3600)];
    }

    public function clearSpectators(Table $table): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(TableSpectator::class, 'ts')
            ->where('ts.table = :tableId')
            ->andWhere('ts.expirationTime < :currentTime')
            ->setParameter('tableId', $table->getId())
            ->setParameter('currentTime', time())
            ->getQuery()
            ->execute();
    }
}
