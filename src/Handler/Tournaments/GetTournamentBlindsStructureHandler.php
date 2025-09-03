<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\Calculator;
use App\Helper\ErrorCodeHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentBlindsStructureHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament = null): array
    {
        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        $lastSB = $tournament->getSetting()->getBlindSetting()->getSmallBlind();
        $lastBB = $tournament->getSetting()->getBlindSetting()->getBigBlind();

        $increase         = $tournament->getSetting()->getTimeBank()->getTime(); //насколько сек повышаем тай банк
        $timeBankInterval = $tournament->getSetting()->getTimeBank()->getPeriodInSec(); //интервал тайм банков
        $blindInterval    = $tournament->getSetting()->getBlindSetting()->getBlindSpeed(); //интвервал блайндов

        $blindsStructure      = [];
        $totalTimePassed      = 0;
        $timeBankUpdatesCount = 0;

        for ($i = 0; $i < 25; $i++) {
            $blindsStructure[] = [
                'interval'   => $tournament->getSetting()->getBlindSetting()->getBlindSpeed() / 60 . ' MIN',
                'level'      => $i + 1,
                'smallBlind' => round($lastSB, 2),
                'bigBlind'   => round($lastBB, 2),
            ];

            $totalTimePassed += $blindInterval;

            $lastSB = Calculator::multiply($tournament->getSetting()->getBlindSetting()->getBlindCoefficient(), $lastSB);
            $lastBB = Calculator::multiply($tournament->getSetting()->getBlindSetting()->getBlindCoefficient(), $lastBB);

            if ($totalTimePassed >= ($timeBankUpdatesCount + 1) * $timeBankInterval) {
                $blindsStructure[count($blindsStructure) - 1]['newTimeBank'] = "+$increase";

                $timeBankUpdatesCount++;
            }
        }

        return $blindsStructure ?: [];
    }
}
