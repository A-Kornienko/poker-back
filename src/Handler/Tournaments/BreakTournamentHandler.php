<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Enum\TournamentStatus;
use App\Handler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class BreakTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    protected function startBreak(Tournament $tournament): bool
    {
        if ($tournament->getSetting()->getBreakSettings()->getPeriod() < 1) {
            return false;
        }

        if ($tournament->getSetting()->getBreakSettings()->getLastTime() > 0) {
            $lastBreakTime = $tournament->getSetting()->getBreakSettings()->getLastTime();
        } else {
            $lastBreakTime = time();

            $tournament->getSetting()->setBreakSettings($tournament->getSetting()->getBreakSettings()->setLastTime($lastBreakTime));
            $this->entityManager->persist($tournament);
            $this->entityManager->flush();
        }

        $startBreakTime  = $lastBreakTime + $tournament->getSetting()->getBreakSettings()->getPeriod();
        $finishBreakTime = $startBreakTime + $tournament->getSetting()->getBreakSettings()->getDuration();
        if (time() < $startBreakTime || time() > $finishBreakTime) {
            return false;
        }

        $breakSettings = $tournament->getSetting()->getBreakSettings();
        $tournament->setStatus(TournamentStatus::Break)->getSetting()
            ->setBreakSettings($breakSettings->setLastTime($finishBreakTime));

        $this->entityManager->persist($tournament);
        $this->entityManager->flush();

        return true;
    }

    protected function finishBreak(Tournament $tournament): bool
    {
        if ($tournament->getSetting()->getBreakSettings()->getPeriod() < 1) {
            return false;
        }

        $lastBreakTime = $tournament->getSetting()->getBreakSettings()->getLastTime();

        if (time() <= $lastBreakTime) {
            return false;
        }

        $tournament->setStatus(TournamentStatus::Started);

        $this->entityManager->persist($tournament);
        $this->entityManager->flush();

        return true;
    }

    public function __invoke(?Tournament $tournament): void
    {
        match ($tournament->getStatus()) {
            TournamentStatus::Started => $this->startBreak($tournament),
            TournamentStatus::Break   => $this->finishBreak($tournament),
            default                   => false,
        };
    }
}
