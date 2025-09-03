<?php

declare(strict_types=1);

namespace App\Event\TableHistory;

use App\Entity\Table;
use App\Event\AbstractEvent;

class FinishGameEvent extends AbstractEvent
{
    public const NAME = 'FinishGame';

    public function __construct(
        private Table $table,
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
}
