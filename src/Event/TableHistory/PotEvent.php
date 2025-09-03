<?php

declare(strict_types=1);

namespace App\Event\TableHistory;

use App\Entity\Table;
use App\Event\AbstractEvent;

class PotEvent extends AbstractEvent
{
    public const NAME = 'PotEvent';

    public function __construct(
        private Table $table,
        private array $banks
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getBanks(): array
    {
        return $this->banks;
    }
}
