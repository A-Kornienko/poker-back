<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Enum\TournamentStatus;
use App\Event\Tournament\Email\EndEvent as EndTournamentEvent;
use App\Handler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class FinishTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
        protected RepeatTournamentHandler $tournamentRepeatHandler,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament): void
    {
        if ($tournament->getStatus()->value === TournamentStatus::Finished->value) {
            return;
        }

        $tournament->setStatus(TournamentStatus::Finished);

        $tournamentUsers = $tournament->getTournamentUsers();
        $tables          = $tournament->getTables();
        foreach ($tables as $table) {
            $table->setIsArchived(true);
            $this->entityManager->persist($table);
        }

        foreach ($tournamentUsers as $tableUser) {
            $this->dispatcher->dispatch(new EndTournamentEvent($tournament, $tableUser->getUser(), $this->translator), EndTournamentEvent::NAME);
        }

        $this->entityManager->persist($tournament);
        $this->entityManager->flush();

        ($this->tournamentRepeatHandler)($tournament);
    }
}
