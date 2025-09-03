<?php

declare(strict_types=1);

namespace App\Handler\Blinds;

use App\Entity\Table;
use App\Enum\TournamentStatus;
use Doctrine\ORM\EntityManagerInterface;

class UpdateTournamentBlindsHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Table $table): void
    {
        if (!$table->getTournament()) {
            return;
        }

        $tournament = $table->getTournament();
        // Если статус турнира - перерыв, обновляем время последнего обновления и сохраняем изменения
        if ($tournament->getStatus() === TournamentStatus::Break) {
            // Проверяем, обновлялись ли блайнды уже во время этого перерыва
            $breakEndTime = $tournament->getLastBlindUpdate() + $tournament->getSetting()->getBreakSettings()->getDuration();
            if (time() < $breakEndTime) {
                return; // Если время текущего момента меньше времени окончания перерыва, то ничего не делаем
            }

            // Обновляем время последнего обновления с учетом перерыва
            $tournament->setLastBlindUpdate($breakEndTime);
            $this->entityManager->persist($tournament);
            $this->entityManager->flush();
        }

        $increaseTimeBlinds = $tournament->getLastBlindUpdate() + $tournament->getSetting()->getBlindSetting()->getBlindSpeed();
        if ($increaseTimeBlinds <= time()) {
            // Берём коэффициент
            $coefficient = $tournament->getSetting()->getBlindSetting()->getBlindCoefficient();
            $table->setSmallBlind($table->getSmallBlind() * $coefficient);
            $table->setBigBlind($table->getBigBlind() * $coefficient);
            $tournament->setLastBlindUpdate(time());
            // Сохраняем изменения
            $this->entityManager->persist($tournament);
            $this->entityManager->persist($table);
        }

        $this->entityManager->flush();
    }
}
