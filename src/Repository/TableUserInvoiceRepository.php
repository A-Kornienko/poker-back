<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TableUserInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableUserInvoice>
 */
class TableUserInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableUserInvoice::class);
    }
}
