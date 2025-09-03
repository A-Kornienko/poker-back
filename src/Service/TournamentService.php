<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Table;
use App\Entity\Tournament;
use App\Entity\TournamentUser;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Event\Tournament\Email\StartEvent as StartTournamentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TournamentService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
        protected TranslatorInterface $translator,
    ) {
    }

    public function start(Tournament $tournament)
    {
        // Устанавливаем турниру статус Started
        $tournament->setStatus(TournamentStatus::Started);
        $tournament->setLastBlindUpdate(time());

        $this->sendEmailToAllPlayers($tournament);

        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
    }

    public function getAvgPlayersInTableForTournament(Tournament $tournament): int
    {
        $tables = $tournament->getTables()->filter(fn($table) => $table->getCountPlayers() > 0);

        $totalUsersCount = $tables->map(fn($table) => $table->getTableUsers()->count())->reduce(fn($carry, $item) => $carry + $item, 0);

        return (int)($totalUsersCount / $tables->count());
    }

    public function copy(Tournament $tournament, $newDateStartRegistration, $newDateEndRegistration): Tournament
    {
        $newTournament = (new Tournament())
            ->setSetting($tournament->getSetting())
            ->setName($tournament->getName())
            ->setDateStart(time() + $tournament->getAutorepeatDate())
            ->setSmallBlind($tournament->getSmallBlind())
            ->setBigBlind($tournament->getBigBlind())
            ->setStatus(TournamentStatus::Pending)
            ->setDateStartRegistration($newDateStartRegistration)
            ->setDateEndRegistration($newDateEndRegistration)
            ->setDescription($tournament->getDescription())
            ->setImage($tournament->getImage())
            ->setAutorepeat($tournament->getAutorepeat())
            ->setAutorepeatDate($tournament->getAutorepeatDate());
        $this->entityManager->persist($newTournament);

        return $newTournament;
    }

    protected function sendEmailToAllPlayers(Tournament $tournament): void
    {
        $tournamentUsers = $tournament->getTournamentUsers();

        foreach ($tournamentUsers as $tournamentUser) {
            $this->dispatcher->dispatch(
                new StartTournamentEvent($tournament, $tournamentUser->getUser(), $this->translator),
                StartTournamentEvent::NAME
            );
        }
    }

    public static function isDeniedRegistration(Tournament $tournament): bool
    {
        return match (true) {
            !$tournament->getSetting()->getStartCountPlayers() && $tournament->getDateStart() < time()             => true,
            $tournament->getStatus() !== TournamentStatus::Pending                                                 => true,
            !$tournament->getSetting()->getStartCountPlayers() && $tournament->getDateStartRegistration() > time() => true,
            !$tournament->getSetting()->getStartCountPlayers() && $tournament->getDateEndRegistration() < time()   => true,
            default                                                                                                => false
        };
    }
}
