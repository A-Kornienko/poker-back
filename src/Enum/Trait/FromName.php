<?php

declare(strict_types=1);

namespace App\Enum\Trait;

trait FromName
{
    public static function fromName(string $name): self
    {
        return constant("self::$name");
    }
}
