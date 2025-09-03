<?php

declare(strict_types=1);

namespace App\Handler\TimeBank;

use App\Entity\Table;
use App\Entity\TableUser;
use App\ValueObject\TableUserTimeBank;
use Doctrine\ORM\EntityManagerInterface;

//TODO: Addd limit condition for increase timebank
class UpdateTimeBankHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function updateUserTimeBankByPeriod(Table $table): void
    {
        $periodInSec = $table->getSetting()->getTimeBank()->getPeriodInSec();

        if (!$periodInSec) {
            return;
        }

        foreach ($table->getTableUsers() as $tableUser) {
            /** @var TableUserTimeBank $tableUserTimeBank */
            $tableUserTimeBank = $tableUser->getTimeBank();
            $lastUpdatedTime   = $tableUserTimeBank->getLastUpdatedTime() + $periodInSec;
            $tableTimeBank     = $table->getSetting()->getTimeBank();

            if (time() >= $lastUpdatedTime) {
                $time = match (true) {
                    ($tableUserTimeBank->getTime() + $tableTimeBank->getTime()) > $tableTimeBank->getTimeLimit() => $tableTimeBank->getTimeLimit(),
                    default                                                                                      => $tableUserTimeBank->getTime() + $tableTimeBank->getTime()
                };

                $tableUserTimeBank->setTime($time);
                $tableUserTimeBank->setLastUpdatedTime(time());

                $tableUser->setTimeBank($tableUserTimeBank);
                $this->entityManager->persist($tableUser);
            }
        }

        $this->entityManager->flush();
    }

    public function updateTimeBankByHandCount(Table $table, array $tableUsers): void
    {
        $countPlayedHand = $table->getSetting()->getTimeBank()->getPeriodInHand();

        if (!$countPlayedHand) {
            return;
        }

        $tableTimeBank = $table->getSetting()->getTimeBank();

        foreach ($tableUsers as $tableUser) {
            /** @var TableUserTimeBank $tableUserTimeBank */
            $tableUserTimeBank = $tableUser->getTimeBank();

            if ($tableUserTimeBank->getCountPlayedHand() > $countPlayedHand) {
                $time = match (true) {
                    $tableUserTimeBank->getTime() + $tableTimeBank->getTime() > $tableTimeBank->getTimeLimit() => $tableTimeBank->getTimeLimit(),
                    default                                                                                    => $tableUserTimeBank->getTime() + $tableTimeBank->getTime()
                };

                $tableUserTimeBank->setTime($time);
                $tableUserTimeBank->setCountPlayedHand(0);
            } else {
                $tableUserTimeBank->setCountPlayedHand($tableUserTimeBank->getCountPlayedHand() + 1);
            }

            $tableUser->setTimeBank($tableUserTimeBank);
            $this->entityManager->persist($tableUser);
        }

        $this->entityManager->flush();
    }

    public function updateTimeBankAfterTurn(TableUser $tableUser): void
    {
        $tableUserTimeBank = $tableUser->getTimeBank();

        if (!$tableUserTimeBank->isActive()) {
            return;
        }

        $currentTime    = time();
        $activationTime = $tableUserTimeBank->getActivationTime();
        $timeBank       = $tableUserTimeBank->getTime();

        // Потраченное время из таймбанка
        $spentTimeBank = $currentTime - $activationTime;
        $remainingTime = $spentTimeBank > 0 ? $timeBank - $spentTimeBank : $timeBank;
        $tableUserTimeBank->setTime($remainingTime < 1 ? 0 : $remainingTime);

        $tableUserTimeBank->setActive(false);
        $tableUser->setTimeBank($tableUserTimeBank);

        $this->entityManager->persist($tableUser);
        $this->entityManager->flush();
    }

    public function updateUserTimeBank(TableUserTimeBank $tableUserTimeBank, Table $table): TableUserTimeBank
    {
        if (!$tableUserTimeBank->getTime() && !$tableUserTimeBank->getActivationTime()) {
            $tableUserTimeBank->setTime($table->getSetting()->getTimeBank()->getTime());
        }

        $tableUserTimeBank->setActive(true);
        $tableUserTimeBank->setActivationTime(time());

        return $tableUserTimeBank;
    }
}
