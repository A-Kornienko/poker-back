<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\TexasHoldem;

use App\Handler\Cards\Combination\Base\BaseCombinationHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.combinationTexasHoldemHandlers')]
interface CombinationHandlerInterface extends BaseCombinationHandlerInterface
{
}
