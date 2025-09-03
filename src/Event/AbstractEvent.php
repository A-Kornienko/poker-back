<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    public const NAME = '';

    public function getName(): string
    {
        return static::NAME;
    }
}
