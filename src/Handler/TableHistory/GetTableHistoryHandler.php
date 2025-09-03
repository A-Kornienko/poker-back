<?php

declare(strict_types=1);

namespace App\Handler\TableHistory;

use App\Entity\{TableHistory, User};
use App\Repository\TableHistoryRepository;
use App\Response\TableHistoryResponse;
use App\Service\PlayerService;

class GetTableHistoryHandler
{
    public function __construct(
        protected TableHistoryRepository $tableHistoryRepository,
        protected PlayerService $playerService
    ) {
    }

    public function __invoke($session, ?User $user): array
    {
        /** @var TableHistory $tableHistory */
        $tableHistory = $this->tableHistoryRepository->findOneBy(['session' => $session]);
        $players      = $tableHistory->getPlayers();
        ksort($players);

        foreach ($players as &$player) {
            $player->setIsMyPlayer($user->getLogin() === $player->getLogin());
            $player->setPosition(
                $this->playerService->getPosition(
                    $player->getPlace(),
                    $tableHistory->getDealer(),
                    array_keys($players)
                )
            );
        }

        $tableHistory->setPlayers($players);

        return TableHistoryResponse::item($tableHistory);
    }
}
