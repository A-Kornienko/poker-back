<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Handler\Notifications\GetNotificationsHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/notifications')]
class NotificationController extends BaseApiController
{
    #[Route(path: '', name: 'sse_get_notifications')]
    public function notifications(
        Request $request,
        GetNotificationsHandler $getNotificationsHandler,
        EntityManagerInterface $entityManager,
    ) {
        $user     = $this->security->getUser();
        $timezone = $request->get('timezone') ?? 'UTC';

        if (!$user) {
            return $this->response([]);
        }

        return $this->streamedResponse(function () use ($getNotificationsHandler, $user, $timezone, $entityManager) {
            ob_end_clean();
            $startTime = time();
            while (true) {
                if (!ob_get_level()) {
                    ob_start();
                }

                $notifications = $getNotificationsHandler($user, $timezone);

                $preparedNotifications = [];
                foreach ($notifications as $notification) {
                    $preparedNotifications[] = $this->renderView($notification->getTemplate(), $notification->getData());

                    break;
                }

                echo "data: " . json_encode(['notifications' => $preparedNotifications]) . "\n\n";

                unset($preparedNotifications, $notifications);

                gc_collect_cycles();

                ob_flush();
                flush();

                if ((time() - $startTime) >= 600) {
                    $entityManager->clear();
                    ob_end_clean();

                    break;
                }

                sleep(15);
            }
        })->send();
    }
}
