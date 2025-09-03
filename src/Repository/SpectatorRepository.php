<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\{Table, TableSpectator};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableSpectator>
 */
class SpectatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableSpectator::class);
    }

    public function getCountSpectators(Table $table): int
    {
        return $this->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('table', $table))
        )
            ->count();
    }
}
