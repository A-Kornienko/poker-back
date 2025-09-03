<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Handler\AbstractHandler;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class StartTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected TournamentService $tournamentService,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
        protected CancelTournamentRegistrationHandler $tournamentCancelRegistrationHandler,
        protected GenerateTournamentTableHandler $generateTournamentTableHandler,
        protected SeatTournamentPlayersHandler $seatTournamentPlayersHandler,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament): void
    {
        if (
            $tournament->getSetting()->getMinCountMembers() > 0
            && $tournament->getSetting()->getMinCountMembers() > $tournament->getTournamentUsers()->count()
        ) {
            foreach ($tournament->getTournamentUsers() as $tournamentUser) {
                $this->tournamentCancelRegistrationHandler->removeMember($tournament, $tournamentUser->getUser());
            }

            return;
        }

        // Получаем массив подходящих столов не в архиве где пользователей меньше 10.
        $tables = ($this->generateTournamentTableHandler)($tournament);
        ($this->seatTournamentPlayersHandler)($tournament, $tables);
        $this->tournamentService->start($tournament);
    }
}
