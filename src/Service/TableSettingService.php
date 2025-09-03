<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TableSetting;
use App\Entity\Tournament;
use App\Enum\Rules;
use App\Enum\TableStyle;
use App\Enum\TableType;
use App\Enum\TournamentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\{TableSettingRepository};

class TableSettingService
{
    public function __construct(
        protected TableSettingRepository $tableSettingRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getCashCollection(
        int $page = 1,
        int $limit = 20,
        string $rule = ''
    ): array {
        $criteria = [
            'type' => TableType::Cash,
        ];
        if ($rule) {
            $criteria['rule'] = Rules::tryFrom($rule);
        }

        $tableSettings = $this->tableSettingRepository->findBy(
            criteria: $criteria,
            limit: $limit,
            offset: ($page - 1) * $limit
        );

        $totalRecords = $this->tableSettingRepository->count($criteria);

        return [
            'items' => $tableSettings,
            'total' => $totalRecords,
        ];
    }

    public function createByTournament(Tournament $tournament): TableSetting
    {
        $tableSetting = new TableSetting();
        $tableSetting->setType(TableType::Tournament)
            ->setName($tournament->getName() . ' Table ')
            ->setBuyIn($tournament->getSetting()->getType() === TournamentType::Paid ? $tournament->getSetting()->getBuyInSettings()->getSum() : 0)
            ->setRule($tournament->getSetting()->getRule())
            ->setStyle(TableStyle::TableBlue->value)
            ->setCurrency('USD')
            ->setSmallBlind($tournament->getSetting()->getBlindSetting()->getSmallBlind())
            ->setBigBlind($tournament->getSetting()->getBlindSetting()->getBigBlind())
            ->setCountCards($tournament->getSetting()->getRule()->countPlayerCards())
            ->setTimeBank($tournament->getSetting()->getTimeBank());

        $this->entityManager->persist($tableSetting);

        return $tableSetting;
    }
}
