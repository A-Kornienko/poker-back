<?php

namespace App\Repository;

use App\Entity\ReformTableQueue;
use App\Entity\Table;
use App\Enum\ReformTableQueueStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ReformTableQueueRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, ReformTableQueue::class);
    }

    public function getOtherReformTableQueues(Table $table, ReformTableQueue $reformTableQueue): array
    {
        return $this->createQueryBuilder('rtq')
            ->where('rtq.tournament = :tournament')
            ->andWhere('rtq.status = :status')
            ->andWhere('rtq.id != :id')
            ->setParameter('tournament', $table->getTournament())
            ->setParameter('status', ReformTableQueueStatus::Process->value)
            ->setParameter('id', $reformTableQueue->getId())
            ->getQuery()
            ->getResult();
    }
}
