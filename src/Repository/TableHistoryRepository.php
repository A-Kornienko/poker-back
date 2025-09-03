<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\{Table, TableHistory};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TableHistory>
 */
class TableHistoryRepository extends ServiceEntityRepository
{
    public const PAGE_LIMIT = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableHistory::class);
    }

    public function getCollection(Table $table, int $page = 1, ?int $limit = null): array
    {
        $limit ??= static::PAGE_LIMIT;
        $offset = ($page - 1) * $limit;

        $items = $this->findBy(
            criteria: [
                'table' => $table,
            ],
            orderBy: ['id' => 'DESC'],
            limit: $limit,
            offset: $offset
        );

        return [
            'items' => array_filter($items, fn(TableHistory $tableHistory) => $tableHistory->getSession() !== $table->getSession()),
            'total' => $this->count(['table' => $table]),
        ];
    }
}
