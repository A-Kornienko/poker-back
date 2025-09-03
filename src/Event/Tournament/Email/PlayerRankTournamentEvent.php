<?php

declare(strict_types=1);

namespace App\Event\Tournament\Email;

use App\Entity\Tournament;
use App\Entity\TournamentUser;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Subscriber\TournamentSendEmailSubscriber;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlayerRankTournamentEvent extends AbstractEvent
{
    public const NAME = 'playerRank';

    public function __construct(
        protected Tournament $tournament,
        protected User $user,
        protected TranslatorInterface $translator
    ) {
    }

    public function execute(TournamentSendEmailSubscriber $tournamentSendEmailSubscriber): void
    {
        $rank = 0;

        $tournamentUser = $this->tournament->getTournamentUsers()->filter(
            fn(TournamentUser $tournamentUser) => $tournamentUser->getUser()->getId() === $this->user->getId()
        );

        if ($tournamentUser->count() > 0) {
            $rank = $tournamentUser->first()->getRank();
        }

        $language = $this->user->getLanguage();

        $data = [
            'to'      => $this->user->getEmail(),
            'subject' => $this->translator->trans('email.tournament.result.subject', [], null, $language),
            'message' => $this->translator->trans('email.tournament.result.message', [
                '{rank}'       => $rank,
                '{tournament}' => $this->tournament->getName(),
            ], null, $language),
        ];

        $tournamentSendEmailSubscriber->sendEmail($data);
    }
}
