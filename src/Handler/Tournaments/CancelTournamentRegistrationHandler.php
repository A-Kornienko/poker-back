<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Enum\TournamentType;
use App\Event\Tournament\Email\UnregisterEvent as UnregisterTournamentEvent;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Handler\Balance\RefundTournamentHandler;
use App\Helper\ErrorCodeHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CancelTournamentRegistrationHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
        protected RefundTournamentHandler $refundTournamentHandler
    ) {
        parent::__construct($security, $translator);
    }

    public function removeMember(Tournament $tournament, User $user): void
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $tournamentUser = $tournament->getTournamentUsers()->filter(
                fn($tournamentUserItem) => $tournamentUserItem->getUser()->getId() === $user->getId()
            )->first() ?: null;

            if ($tournamentUser) {
                $tournament->removeTournamentUser($tournamentUser);
                $this->entityManager->remove($tournamentUser);
                $this->entityManager->persist($tournament);
                $this->entityManager->flush();
            }

            if ($tournament->getSetting()->getType() === TournamentType::Paid && $tournament->getSetting()->getEntrySum()) {
                ($this->refundTournamentHandler)($tournament, $user);
            }

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откат транзакции в случае ошибки
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }

    protected function validate(?User $user, ?Tournament $tournament): void
    {
        match (true) {
            !$user => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::MAIN_USER_NOT_FOUND
            ),

            !$tournament => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::NO_TOURNAMENT
            ),

            $tournament->getStatus() !== TournamentStatus::Pending => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::CANCEL_REGISTRATION_IS_OVER
            ),

            $tournament->getDateEndRegistration() && $tournament->getDateEndRegistration() < time() => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::CANCEL_REGISTRATION_IS_OVER
            ),

            $tournament->getDateStart() < time() => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::TOURNAMENT_HAS_STARTED
            ),

            !$this->isUserInTournament($tournament, $user) => ResponseException::makeExceptionByCode(
                $this->translator,
                ErrorCodeHelper::USER_NOT_IN_TOURNAMENT
            ),

            default => null,
        };
    }

    protected function isUserInTournament(Tournament $tournament, User $user): bool
    {
        foreach ($tournament->getTournamentUsers() as $tournamentUserItem) {
            if ($tournamentUserItem->getUser()->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    public function __invoke(?Tournament $tournament): void
    {
        $user = $this->security->getUser();

        $this->validate($user, $tournament);
        $this->removeMember($tournament, $user);

        $this->entityManager->flush();

        $this->dispatcher->dispatch(new UnregisterTournamentEvent($tournament, $user, $this->translator), UnregisterTournamentEvent::NAME);
    }
}
