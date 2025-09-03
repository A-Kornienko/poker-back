<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Bank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<History>
 *
 * @method Bank|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bank|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bank[]    findAll()
 * @method Bank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankRepository extends ServiceEntityRepository
{
    /**
     * Page limit for pagination
     */
    public const pageLimit = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bank::class);
    }
}
