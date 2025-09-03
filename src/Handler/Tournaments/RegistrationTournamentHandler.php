<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Event\Tournament\Email\RegisterEvent as RegisterTournamentEvent;
use App\Handler\AbstractHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationTournamentHandler extends AbstractHandler
{
    public function __construct(
        #[TaggedIterator('app.tournamentRegistration')]
        protected iterable $tournamentRegistration,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected StartTournamentHandler $tournamentStartHandler,
        protected EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament): void
    {
        $user = $this->security->getUser();
        foreach ($this->tournamentRegistration as $registration) {
            if ($registration->isApplicable($tournament)) {
                $registration($tournament, $user);

                break;
            }
        }

        $this->dispatcher->dispatch(
            new RegisterTournamentEvent($tournament, $user, $this->translator),
            RegisterTournamentEvent::NAME
        );

        if ($tournament->getSetting()->getStartCountPlayers() > 0
            && $tournament->getSetting()->getStartCountPlayers() === $tournament->getTournamentUsers()->count()
        ) {
            ($this->tournamentStartHandler)($tournament);
        }
    }
}
