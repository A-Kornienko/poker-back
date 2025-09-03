<?php

declare(strict_types=1);

namespace App\Handler\Notifications\UserNotifications;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Enum\UserNotificationType;
use App\Repository\NotificationRepository;
use App\Repository\TournamentRepository;
use App\Repository\TournamentUserRepository;
use App\ValueObject\Notification;
use Doctrine\ORM\EntityManagerInterface;

class StartTournamentNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        protected TournamentRepository $tournamentRepository,
        protected TournamentUserRepository $tournamentUserRepository,
        protected NotificationRepository $notificationRepository,
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function getNotification(User $user, $timezone): ?array
    {
        $notifications = [];
        $tournaments   = $user->getTournaments();
        /** @var Tournament $tournament */
        foreach ($tournaments as $tournament) {
            if ($tournament->getStatus()->value !== TournamentStatus::Started->value) {
                continue;
            }

            $tableId = $this->tournamentUserRepository->findOneBy(['tournament' => $tournament, 'user' => $user])
                ->getTable()?->getId();

            if (!$tableId) {
                break;
            }

            $actualMessage = $this->notificationRepository->findOneBy(['tournament' => $tournament, 'user' => $user, 'type' => UserNotificationType::TournamentStart]);

            if ($actualMessage) {
                continue;
            }

            $notificationEntity = (new \App\Entity\Notification())
                ->setTournament($tournament)
                ->setUser($user)
                ->setMessage($tournament->getName() . " tournament has started ")
                ->setType(UserNotificationType::TournamentStart);

            $this->entityManager->persist($notificationEntity);
            $this->entityManager->flush();

            $notifications[] = (new Notification())
                ->setTemplate(UserNotificationType::TournamentStart->getTemplate())
                ->addData('tournamentName', $tournament->getName())
                ->addData('tableId', $tableId);
        }

        return $notifications;
    }
}
