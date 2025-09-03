<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TournamentPrize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TournamentPrize>
 */
class TournamentPrizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentPrize::class);
    }
}
