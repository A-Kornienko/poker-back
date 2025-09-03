<?php

declare(strict_types=1);

namespace App\Handler\Tournaments\Registration;

use api\exchange\Currency;
use App\Entity\Tournament;
use App\Entity\TournamentUser;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Exception\ResponseException;
use App\Helper\ErrorCodeHelper;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTournamentRegistration implements TournamentRegistrationInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TournamentService $tournamentService,
        protected TranslatorInterface $translator
    ) {
    }

    protected function createPlayer(Tournament $tournament, User $user): TournamentUser
    {
        $tournamentUser = (new TournamentUser())
            ->setTournament($tournament)
            ->setUser($user);
        $tournament->addTournamentUser($tournamentUser);

        $this->entityManager->persist($tournamentUser);
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();

        return $tournamentUser;
    }

    protected function validateLateRegistration(?Tournament $tournament, ?User $user): void
    {
        if (!$user) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::MAIN_USER_NOT_FOUND);
        }

        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        foreach ($tournament->getTournamentUsers() as $tournamentUser) {
            if ($tournamentUser->getUser()->getId() === $user->getId()) {
                ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::USER_ALREADY_REGISTERED_IN_TOURNAMENT);
            }
        }

        if (!$tournament->getSetting()->getLateRegistration()->getTimeAfterStart() || !$tournament->getSetting()->getLateRegistration()->getMaxBlindLevel()) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_LATE_TOURNAMENT_REGISTRATION);
        }

        if ($tournament->getDateEndRegistration() > time()) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::LATE_REGISTRATION_NOT_STARTED);
        }

        if (
            $tournament->getSetting()->getLateRegistration()->getMaxBlindLevel() &&
            $tournament->getBlindLevel() === $tournament->getSetting()->getLateRegistration()->getMaxBlindLevel()
        ) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOO_BIG_BLIND_LEVEL);
        }

        if ($tournament->getSetting()->getLateRegistration()->getTimeAfterStart() && $tournament->getDateStart(
            ) + $tournament->getSetting()->getLateRegistration()->getTimeAfterStart() < time()) {

            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TIME_OVER_LATE_REGISTRATION);
        }

        if ($tournament->getTournamentUsers()->count() === $tournament->getSetting()->getLimitMembers()
            || ($tournament->getSetting()->getStartCountPlayers() > 0 && $tournament->getSetting()->getStartCountPlayers() === $tournament->getTournamentUsers()->count())
        ) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_IS_FULL);
        }
    }

    protected function validateDefaultRegistration(?Tournament $tournament, ?User $user): void
    {
        if (!$user) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::MAIN_USER_NOT_FOUND);
        }

        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        if ($tournament->getStatus() !== TournamentStatus::Pending) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_HAS_STARTED);
        }

        foreach ($tournament->getTournamentUsers() as $tournamentUser) {
            if ($tournamentUser->getUser()->getId() === $user->getId()) {
                ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::USER_ALREADY_REGISTERED_IN_TOURNAMENT);
            }
        }

        match (true) {
            $tournament->getDateStartRegistration() > time() => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::REGISTRATION_NOT_STARTED_YET),
            $tournament->getDateEndRegistration() < time()   => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::REGISTRATION_IS_OVER),
            default                                          => true
        };

        if ($tournament->getDateStart() < time() && $tournament->getSetting()->getStartCountPlayers() === 0) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_HAS_STARTED);
        }

        if ($tournament->getTournamentUsers()->count() === $tournament->getSetting()->getLimitMembers()
            || ($tournament->getSetting()->getStartCountPlayers() > 0 && $tournament->getSetting()->getStartCountPlayers() === $tournament->getTournamentUsers()->count())
        ) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TOURNAMENT_IS_FULL);
        }
    }

    protected function validateUsdBalance(Tournament $tournament, User $user): void
    {
        $mainUser         = $user->getMainUser();
        if (!$mainUser) {
            return;
        }
        $actualUsdBalance = Currency::converter($mainUser->getBalance(), $mainUser->getUserInfo()['currency'], 'USD');

        if ($tournament->getSetting()->getEntrySum() > $actualUsdBalance) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::SMALL_BALANCE);
        }
    }
}
