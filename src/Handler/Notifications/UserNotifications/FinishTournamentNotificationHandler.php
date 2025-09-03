<?php

declare(strict_types=1);

namespace App\Handler\Notifications\UserNotifications;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Enum\UserNotificationType;
use App\Repository\NotificationRepository;
use App\ValueObject\Notification;
use Doctrine\ORM\EntityManagerInterface;

class FinishTournamentNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        protected NotificationRepository $notificationRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getNotification(User $user, $timezone): ?array
    {
        $notifications = [];
        $tournaments   = $user->getTournaments();
        /** @var Tournament $tournament */
        foreach ($tournaments as $tournament) {
            if ($tournament->getStatus()->value !== TournamentStatus::Finished->value) {
                continue;
            }

            $actualMessage = $this->notificationRepository->findOneBy(['tournament' => $tournament, 'user' => $user, 'type' => UserNotificationType::TournamentFinish]);

            if ($actualMessage) {
                continue;
            }

            $notificationEntity = (new \App\Entity\Notification())
                ->setTournament($tournament)
                ->setUser($user)
                ->setMessage($tournament->getName() . " tournament has been ended ")
                ->setType(UserNotificationType::TournamentFinish);

            $this->entityManager->persist($notificationEntity);
            $this->entityManager->flush();

            $notifications[] = (new Notification())
                ->setTemplate(UserNotificationType::TournamentFinish->getTemplate())
                ->addData('tournamentName', $tournament->getName());
        }

        return $notifications;
    }
}
