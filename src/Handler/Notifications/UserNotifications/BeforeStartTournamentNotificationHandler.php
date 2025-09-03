<?php

declare(strict_types=1);

namespace App\Handler\Notifications\UserNotifications;

use App\Entity\Notification as NotificationEntity;
use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Enum\UserNotificationType;
use App\Helper\DateTimeHelper;
use App\Repository\NotificationRepository;
use App\Repository\TournamentRepository;
use App\ValueObject\Notification;
use Doctrine\ORM\EntityManagerInterface;

class BeforeStartTournamentNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        protected TournamentRepository $tournamentRepository,
        protected EntityManagerInterface $entityManager,
        protected NotificationRepository $notificationRepository
    ) {
    }

    public function getNotification(User $user, $timezone): ?array
    {
        $currentTime   = time();
        $notifications = [];
        $tournaments   = $user->getTournaments();

        /** @var Tournament $tournament */
        foreach ($tournaments as $tournament) {
            if ($tournament->getStatus()->value !== TournamentStatus::Pending->value) {
                continue;
            }

            $actualMessage = $this->notificationRepository->findOneBy(['tournament' => $tournament, 'user' => $user , 'type' => UserNotificationType::TournamentBeforeStart]);

            if ($actualMessage) {
                continue;
            }

            if ($currentTime > $tournament->getDateStart() - (15 * 60)) {
                $dateStart          = DateTimeHelper::formatted($tournament->getDateStart(), 'Y-m-d H:i:s', $timezone);
                $notificationEntity = (new NotificationEntity())
                    ->setTournament($tournament)
                    ->setUser($user)
                    ->setMessage($tournament->getName() . " tournament will start in " . $dateStart)
                    ->setType(UserNotificationType::TournamentBeforeStart);

                $this->entityManager->persist($notificationEntity);
                $this->entityManager->flush();

                $notification = (new Notification())
                    ->setTemplate(UserNotificationType::TournamentBeforeStart->getTemplate())
                    ->addData('tournamentName', $tournament->getName())
                    ->addData('tournamentStartTime', $dateStart);

                $notifications[] = $notification;
            }
        }

        return $notifications;
    }
}
