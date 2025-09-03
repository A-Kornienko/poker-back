<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination\OmahaHigh;

use App\Handler\Cards\Combination\Base\BaseCombinationHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.combinationOmahaHighHandlers')]
interface CombinationHandlerInterface extends BaseCombinationHandlerInterface
{
}
