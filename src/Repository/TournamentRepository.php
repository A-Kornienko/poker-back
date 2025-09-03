<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tournament;
use App\Enum\Rules;
use App\Enum\TournamentStatus;
use App\Enum\TournamentType;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournament>
 */
class TournamentRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Tournament::class);
    }

    public function getPendingTournaments(): array
    {
        $query = $this->entityManager->createQueryBuilder();

        $query->select('t')
            ->from(Tournament::class, 't')
            ->innerJoin('t.setting', 's')
            ->where('t.dateStart <= :currentTime')
            ->andWhere('t.status = :status')
            ->andWhere('s.startCountPlayers < :startCountPlayers')
            ->setParameter('currentTime', time())
            ->setParameter('status', TournamentStatus::Pending)
            ->setParameter('startCountPlayers', 1);

        return $query->getQuery()->getResult();
    }

    public function getFilters(): array
    {
        $query = $this->createQueryBuilder('t');

        $query->select(
            'MIN(t.entrySum) as minEntrySum',
            'MAX(t.entrySum) as maxEntrySum',
            'MIN(t.dateStart) as minDateStart',
            'MAX(t.dateStart) as maxDateStart',
            'MIN(t.limitMembers) as minLimitMembers',
            'MAX(t.limitMembers) as maxLimitMembers',
            'MIN(p.sum) as minSum',
        )
            ->leftJoin('t.prizes', 'p');

        $result = $query->getQuery()->getOneOrNullResult();

        $maxSumQuery = $this->createQueryBuilder('t')
            ->select('SUM(p.sum) as totalSumPrizes')
            ->leftJoin('t.prizes', 'p')
            ->groupBy('t.id')
            ->orderBy('totalSumPrizes', 'DESC')
            ->setMaxResults(1);

        $maxTournamentSum = $maxSumQuery->getQuery()->getOneOrNullResult();

        $hasTournamentWithoutPrizes = $this->createQueryBuilder('t')
            ->leftJoin('t.prizes', 'p')
            ->select('COUNT(t.id)')
            ->where('p.id IS NULL')
            ->getQuery()
            ->getSingleScalarResult() > 0;

        return [
            'status' => TournamentStatus::getPublic(),
            'type'   => TournamentType::toArray(),
            'buy_in' => [
                'min' => (float) $result['minEntrySum'],
                'max' => (float) $result['maxEntrySum']
            ],
            'date_start' => [
                'min' => $result['minDateStart'],
                'max' => $result['maxDateStart']
            ],
            'limit_members' => [
                'min' => (int) $result['minLimitMembers'],
                'max' => (int) $result['maxLimitMembers']
            ],
            'sum' => [
                'min' => $hasTournamentWithoutPrizes ? 0 : (float) $result['minSum'],
                'max' => (float) ($maxTournamentSum['totalSumPrizes'] ?? 0)
            ]
        ];
    }

    public function getCollection(
        array $buyIn,
        int $page = 1,
        int $limit = 20,
        string $rule = '',
        string $type = ''
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.setting', 's')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        if (!empty($buyIn)) {
            $minSum = $buyIn['min'] ?? null;
            $maxSum = $buyIn['max'] ?? null;

            if ($minSum && $maxSum) {
                $qb->andWhere('s.entrySum BETWEEN :minSum AND :maxSum')
                    ->setParameter('minSum', $minSum)
                    ->setParameter('maxSum', $maxSum);
            } elseif ($minSum && !$maxSum) {
                $qb->andWhere('s.entrySum >= :minSum')
                    ->setParameter('minSum', $minSum);
            }
        }

        if ($type) {
            $qb->andWhere('s.type = :type')
                ->setParameter('type', TournamentType::tryFrom($type));
        }

        if ($rule) {
            $qb->andWhere('s.rule = :rule')
                ->setParameter('rule', Rules::tryFrom($rule));
        }

        $tournaments = $qb->getQuery()->getResult();

        $totalCount = (clone $qb)
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'items' => $tournaments,
            'total' => (int) $totalCount,
        ];
    }

    protected function convertDateToTimestamp(string $dateString, $isMax = false): ?int
    {
        $date = DateTime::createFromFormat('Ymd', $dateString);

        return match (true) {
            $isMax  => $date->setTime(23, 59, 59)->getTimestamp(),
            default => $date->setTime(0, 0, 0)->getTimestamp()
        };
    }
}
