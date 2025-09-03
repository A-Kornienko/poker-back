<?php

declare(strict_types=1);

namespace App\Event\Tournament\Email;

use App\Entity\Tournament;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Subscriber\TournamentSendEmailSubscriber;
use Symfony\Contracts\Translation\TranslatorInterface;

class UnregisterEvent extends AbstractEvent
{
    public const NAME = 'unregisterTournament';

    public function __construct(
        protected Tournament $tournament,
        protected User $user,
        protected TranslatorInterface $translator
    ) {
    }

    public function execute(TournamentSendEmailSubscriber $tournamentSendEmailSubscriber): void
    {
        $language = $this->user->getLanguage();

        $data = [
            'to'      => $this->user->getEmail(),
            'subject' => $this->translator->trans('email.tournament.unregistered.subject', [], null, $language),
            'message' => $this->translator->trans('email.tournament.unregistered.message', [
                '{tournament}' => $this->tournament->getName(),
            ], null, $language),
        ];

        $tournamentSendEmailSubscriber->sendEmail($data);
    }
}
