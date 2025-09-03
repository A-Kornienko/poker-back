<?php

declare(strict_types=1);

namespace App\Handler\Notifications\UserNotifications;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.userNotificationHandlers')]
interface NotificationHandlerInterface
{
    public function getNotification(User $user, $timezone): ?array;
}
