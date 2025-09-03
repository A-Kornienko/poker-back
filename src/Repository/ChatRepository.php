<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\{Chat, Table};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    public const PAGE_LIMIT = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * @deprecated
     */
    public function getMessages(int $lastId, Table $table): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id > :lastId')
            ->andWhere('c.table = :tableId')
            ->setParameter('lastId', $lastId)
            ->setParameter('tableId', $table->getId())
            ->getQuery()->getResult();
    }

    public function getCollection(Table $table, int $page = 1, ?int $limit = null): array
    {
        $limit ??= static::PAGE_LIMIT;
        $offset = ($page - 1) * $limit;

        return [
            'items' => $this->findBy(['table' => $table], ['id' => 'ASC'], $limit, $offset),
            'total' => $this->count(['table' => $table]),
        ];
    }

    /**
     * @deprecated
     */
    public function getCountMessages(Table $table): int
    {
        return $this->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('table', $table))
        )
            ->count();
    }
}
