<?php

declare(strict_types=1);

namespace App\Handler\Notifications;

use App\Entity\User;
use App\Handler\AbstractHandler;
use App\Handler\Notifications\UserNotifications\NotificationHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class GetNotificationsHandler extends AbstractHandler
{
    protected iterable $notificationHandlers;

    public function __construct(
        #[TaggedIterator('app.userNotificationHandlers')]
        iterable $notificationHandlers,
        protected EntityManagerInterface $entityManager,
    ) {
        $this->notificationHandlers = $notificationHandlers;
    }

    public function __invoke(User $user, $timezone): array
    {
        $notifications = [];
        /** @var NotificationHandlerInterface $notificationHandler */
        foreach ($this->notificationHandlers as $notificationHandler) {
            $notification = $notificationHandler->getNotification($user, $timezone);
            if (is_array($notification)) {
                $notifications = array_merge($notifications, $notification);
            }
        }

        return $notifications;
    }
}
