<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Entity\TournamentPrize;
use App\Handler\AbstractHandler;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepeatTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected TournamentService $tournamentService,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Tournament $tournament): void
    {
        if (!$tournament->getAutorepeat()) {
            return;
        }

        $newDateStartRegistration = (time() + $tournament->getAutorepeatDate()) - ($tournament->getDateStart() - $tournament->getDateStartRegistration());
        $newDateEndRegistration   = (time() + $tournament->getAutorepeatDate()) - ($tournament->getDateStart() - $tournament->getDateEndRegistration());

        $newTournament = $this->tournamentService->copy($tournament, $newDateStartRegistration, $newDateEndRegistration);

        foreach ($tournament->getPrizes() as $tournamentPrize) {
            $newPrize = (new TournamentPrize())
                ->setTournament($newTournament)
                ->setSum($tournamentPrize->getSum());

            $this->entityManager->persist($newPrize);
        }

        $this->entityManager->flush();
    }
}
