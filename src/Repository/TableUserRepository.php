<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\TableType;
use App\Entity\{Table, TableSetting, TableUser, User};
use App\Enum\TableUserStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableUser>
 *
 * @method TableUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableUser::class);
    }

    public function getMaxBetExcludeCurrentUser(Table $table, User $user): ?float
    {
        $maxBet = $this->createQueryBuilder('tu')
            ->select('MAX(tu.bet)')
            ->where('tu.table = :tableId')
            ->andWhere('tu.user != :userId')
            ->setParameter('tableId', $table->getId())
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return round((float)$maxBet, 2);
    }

    public function getMaxBet(Table $table): ?float
    {
        $maxBet = $this->createQueryBuilder('tu')
            ->select('MAX(tu.bet)')
            ->where('tu.table = :tableId')
            ->setParameter('tableId', $table->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return round((float)$maxBet, 2);
    }

    public function getPlayersSortedByPlace(Table $table, ?array $playeStatus = null): array
    {
        $playeStatus = $playeStatus ?? [TableUserStatus::Active->value];
        $activeTableUsers = $this->findBy(
            criteria: [
                'table'  => $table,
                'status' => $playeStatus
            ],
            orderBy: ['place' => 'ASC']
        );

        $sortedActiveTableUsers = [];
        foreach ($activeTableUsers as $tableUser) {
            $sortedActiveTableUsers[$tableUser->getPlace()] = $tableUser;
        }

        return $sortedActiveTableUsers;
    }

    public function getPlayersSortedByBetSumAsc(Table $table): array
    {
        return $this->findBy(
            criteria: [
                'table' => $table,
            ],
            orderBy: ['betSum' => 'ASC']
        );
    }

    public function getCollectionByTableSetting(TableSetting $tableSetting): array
    {
        return $this->createQueryBuilder('tu')
            ->innerJoin('tu.table', 't')
            ->where('t.setting = :settingId')
            ->setParameter('settingId', $tableSetting->getId())
            ->getQuery()
            ->getResult();
    }

    public function getLosersByUpdatedTime(): array
    {
        return $this->createQueryBuilder('tu')
            ->where('tu.status = :status')
            ->andWhere('tu.updatedAt < :time')
            ->setParameter('status', TableUserStatus::Lose)
            ->setParameter('time', time() - 60)
            ->getQuery()
            ->getResult();
    }

    public function getAfkPlayers(): array
    {
        return $this->createQueryBuilder('tu')
            ->innerJoin('tu.table', 't')
            ->innerJoin('t.setting', 'ts')
            ->where('tu.seatOut IS NOT NULL')
            ->andWhere('ts.type = :type')
            ->setParameter('type', TableType::Cash)
            ->getQuery()
            ->getResult();
    }
}
