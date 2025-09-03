<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Enum\BetType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.betHandlers')]
interface BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool;

    public static function getDefaultPriority(): int;
}
