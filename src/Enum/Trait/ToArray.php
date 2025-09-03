<?php

declare(strict_types=1);

namespace App\Enum\Trait;

trait ToArray
{
    public function asArray(): array
    {
        return ['name' => $this->name, 'value' => $this->value];
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(self::values(), self::names());
    }
}
