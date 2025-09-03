<?php

declare(strict_types=1);

namespace App\Event\TableHistory;

use App\Event\AbstractEvent;

class WinnerEvent extends AbstractEvent
{
    public const NAME = 'WinnerEvent';

    public function __construct(
        private object|array $winners
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getWinners(): array|object
    {
        return $this->winners;
    }
}
