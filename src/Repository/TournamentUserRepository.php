<?php

namespace App\Repository;

use App\Entity\TournamentUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class TournamentUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, protected EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, TournamentUser::class);
    }
}
