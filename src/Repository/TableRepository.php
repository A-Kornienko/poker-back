<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Table;
use App\Entity\TableSetting;
use App\Entity\Tournament;
use App\Enum\TableState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Table>
 */
class TableRepository extends ServiceEntityRepository
{
    private const DEFAULT_LIMIT = 20;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Table::class);
    }

    public function getTablesForTakePlayers(int $tournamentId, array $excludedTableIds): array
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(Table::class, 't');

        $sql = '
            SELECT t.*, COUNT(tu.id) AS userCount
            FROM `table` t
            LEFT JOIN `table_user` tu ON t.id = tu.table_id
            WHERE t.tournament_id = :tournamentId
            AND t.state = :state
        ';

        if (!empty($excludedTableIds)) {
            $sql .= ' AND t.id NOT IN (:excludedTableIds)';
        }

        $sql .= '
            GROUP BY t.id
            HAVING COUNT(tu.id) > 5
            ORDER BY userCount DESC
        ';

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('tournamentId', $tournamentId);
        $query->setParameter('state', TableState::Init);

        if (!empty($excludedTableIds)) {
            $query->setParameter('excludedTableIds', implode(',', $excludedTableIds));
        }

        return $query->getResult();
    }

    public function getTournamentTablesForSeat(int $tournamentId, $excludedTableIds): array
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(Table::class, 't');

        $sql = 'SELECT t.*, COUNT(tu.id) AS userCount
            FROM `table` t
            LEFT JOIN `table_user` tu ON t.id = tu.table_id
            WHERE t.tournament_id = :tournamentId';

        if (!empty($excludedTableIds)) {
            $sql .= ' AND t.id NOT IN (:excludedTableIds)';
        }

        $sql .= ' GROUP BY t.id
            HAVING COUNT(tu.id) BETWEEN 5 AND 10
            ORDER BY userCount ASC';

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('tournamentId', $tournamentId);

        if (!empty($excludedTableIds)) {
            $query->setParameter('excludedTableIds', implode(',', $excludedTableIds));
        }

        return $query->getResult();
    }

    public function getTournamentCollection(Tournament $tournament, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $query = $this->createQueryBuilder('t')
            ->where('t.tournament = :tournament')
            ->setParameter('tournament', $tournament)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    public function getMaxNumber(TableSetting $setting)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('max_number', 'maxNumber');

        $query = $this->entityManager->createNativeQuery(
            'SELECT MAX(`number`) AS max_number FROM `table` WHERE `setting_id` = :settingId',
            $rsm
        );

        $query->setParameter('settingId', $setting->getId());

        return $query->getSingleScalarResult();
    }

    public function findTournamentTableByMinPlayers(Tournament $tournament): ?Table
    {
        $sql = '
           SELECT t.*,
           COUNT(tu.id) AS user_count,
           ts.count_players
           FROM `table` t
           LEFT JOIN `table_user` tu ON tu.table_id = t.id
           LEFT JOIN `table_setting` ts ON t.setting_id = ts.id
           WHERE t.tournament_id = :tournamentId
           GROUP BY t.id
           HAVING user_count < ts.count_players
           ORDER BY user_count ASC
           LIMIT 1;
        ';

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata(Table::class, 't');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('tournamentId', $tournament->getId());

        return $query->getOneOrNullResult();
    }
}
