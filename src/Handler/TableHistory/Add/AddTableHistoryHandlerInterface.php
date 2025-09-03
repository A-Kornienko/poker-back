<?php

declare(strict_types=1);

namespace App\Handler\TableHistory\Add;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\EventDispatcher\Event;

#[AutoconfigureTag('app.tableHistory')]
interface AddTableHistoryHandlerInterface
{
    public function getRelatedEvent(): string;

    public function __invoke(Event $event): void;
}
