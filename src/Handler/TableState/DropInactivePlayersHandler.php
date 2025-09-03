<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Entity\TableUser;
use App\Handler\AbstractHandler;
use App\Handler\Cards\Combination\CompareCombinationsHandler;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class DropInactivePlayersHandler extends AbstractHandler
{
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        protected CompareCombinationsHandler $compareCombinationsHandler,
        protected EntityManagerInterface $entityManager,
        protected TableUserRepository $tableUserRepository,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(int $startTime): void
    {
        for ($i = 0; $i < 12; $i++) {
            $players = $this->tableUserRepository->getLosersByUpdatedTime();

            if (!$players) {
                continue;
            }

            $tournamentPlayers = [];
            $cashPlayers = [];

            /** @var TableUser $player */
            foreach ($players as $player) {
                $player->getTable()->getTournament() ? $tournamentPlayers[] = $player : $cashPlayers[] = $player;
            }

            try {
                foreach ($cashPlayers as $player) {
                    $this->entityManager->remove($player);
                }

                $this->dropTournamentPlayers($tournamentPlayers);
                $this->entityManager->flush();

                if ($startTime <= time()) {
                    $this->entityManager->getConnection()->close();
                    die();
                }
            } catch (\Throwable $e) {
                $this->entityManager->getConnection()->close();
            }
        }
        sleep(5);
    }


    protected function groupPlayersByTableAndUpdatedAt(array $players): array
    {
        $groupedByTableAndTime = [];

        foreach ($players as $player) {
            if (!$player->getTable()->getTournament()) {
                continue;
            }

            $tableId = $player->getTable()->getId();
            $updatedAt = $player->getUpdatedAt();
            $groupedByTableAndTime[$tableId][$updatedAt][] = $player;
        }

        return $groupedByTableAndTime;
    }

    protected function dropTournamentPlayers(array $tournamentPlayers): void
    {
        $groupedByTableAndTime = $this->groupPlayersByTableAndUpdatedAt($tournamentPlayers);

        foreach ($groupedByTableAndTime as $timeGroups) {
            foreach ($timeGroups as $players) {
                if (count($players) > 0 && reset($players)->getCombination()) {
                    usort($players, function ($a, $b) {
                        $combinationA = $a->getCombination();
                        $combinationB = $b->getCombination();

                        if ($combinationA->getRank() === $combinationB->getRank()) {
                            return ($this->compareCombinationsHandler)(
                                $combinationA->getCards(),
                                $combinationB->getCards()
                            );
                        }

                        return $combinationB->getRank() <=> $combinationA->getRank();
                    });
                }

                foreach ($players as $player) {
                    $this->entityManager->remove($player);
                    unset($player);
                }
            }

            $this->entityManager->flush();
        }
    }
}
