<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Entity\TournamentUser;
use App\Handler\AbstractHandler;
use App\Service\PlayerService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeatTournamentPlayersHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected PlayerService $playerService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Tournament $tournament, Collection $tables): void
    {
        $groupedFreeTablePlaces = [];
        $indexedFreeTables      = [];
        foreach ($tables->toArray() as $freeTable) {
            $indexedFreeTables[$freeTable->getId()]      = $freeTable;
            $groupedFreeTablePlaces[$freeTable->getId()] = $freeTable->getFreePlaces();
        }

        $freeTournamentUsers = $tournament->getTournamentusers()->filter(
            fn(TournamentUser $tournamentUser) => !$tournamentUser->getTable()
        )->toArray();

        // Сажаем 1 пользователя за 1 место стола,
        // на следующей итерации переходим на другой стол и сажаем следующего пользователя на свободное место
        // То-есть в каждой итерации мы меняем стол и сажаем пользователей сразу на разные столы на свободные места
        // После посадки исключаем свободные места из общего списка, чтоб следующие пользователи занимали только свободные места

        $freeTableId = key($groupedFreeTablePlaces);
        $tableForSit = $indexedFreeTables[$freeTableId];
        foreach ($freeTournamentUsers as $freeTournamentUser) {
            $this->playerService->create(
                $tableForSit,
                $freeTournamentUser->getUser(),
                array_shift($groupedFreeTablePlaces[$freeTableId]),
                $tournament->getSetting()->getEntryChips()
            );
            $freeTournamentUser->setTable($tableForSit);
            $this->entityManager->persist($freeTournamentUser);

            if (count($groupedFreeTablePlaces[$freeTableId]) < 1) {
                unset($groupedFreeTablePlaces[$freeTableId]);
            }

            $freeTablePlaces = next($groupedFreeTablePlaces);
            $freeTablePlaces ?: reset($groupedFreeTablePlaces);
            $freeTableId = key($groupedFreeTablePlaces);

            if (!$freeTableId) {
                break;
            }

            $tableForSit = $indexedFreeTables[$freeTableId];
        }
    }
}
