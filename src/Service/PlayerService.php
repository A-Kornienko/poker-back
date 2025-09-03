<?php

declare(strict_types=1);

namespace App\Service;

use App\Handler\Balance\ReturnRemainingsPlayerBalanceHandler;
use App\Entity\{Table, TableUser, User};
use App\Enum\BetType;
use App\Enum\PlayerPosition;
use App\Enum\TableUserStatus;
use App\Exception\ResponseException;
use App\Handler\Cards\Combination\CompareCombinationsHandler;
use App\Helper\Calculator;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Repository\TournamentUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlayerService
{
    public function __construct(
        protected TableUserRepository $tableUserRepository,
        protected TournamentUserRepository $tournamentUserRepository,
        protected EntityManagerInterface $entityManager,
        protected TranslatorInterface $translator,
        protected CompareCombinationsHandler $compareCombinationsHandler,
        protected ReturnRemainingsPlayerBalanceHandler $returnRemainingsPlayerBalanceHandler
    ) {
    }

    public function create(Table $table, User $user, int $place, float $stack): TableUser
    {
        $player = (new TableUser())
            ->setTable($table)
            ->setUser($user)
            ->setStack($stack)
            ->setPlace($place)
            ->setStatus(TableUserStatus::Pending)
            ->setUpdatedAt(time());

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    /**
     * @param TableUser $player
     * @param Table $newTable
     * @param int $place
     * @return TableUser
     */
    public function changeTable(TableUser $player, Table $newTable, int $place): TableUser
    {
        $player->setTable($newTable);
        $player->setPlace($place);
        $player->setStatus(TableUserStatus::Pending);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    public function getTableUser(Table $table, User $user): TableUser
    {
        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user,
            'table' => $table
        ]);

        if (!$player) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::PLAYER_NOT_FOUND);
        }

        return $player;
    }

    public function setSeatOut(TableUser $player): TableUser
    {
        if (!$player->getSeatOut()) {
            $player->setSeatOut(time());
        }

        return $player;
    }

    public function refreshPlayers(Table $table): void
    {
        $players = $this->tableUserRepository->findBy(['table' => $table]);
        foreach ($players as $player) {
            if (
                $player->getStatus() === TableUserStatus::Pending || 
                $player->getStatus() === TableUserStatus::Winner
            ) {
                $player->setStatus(TableUserStatus::Active);
            }

            $player->setBet(0)
                ->setBetSum(0)
                ->setBetType(null)
                ->setBetExpirationTime(0)
                ->removeCards();
            $this->entityManager->persist($player);
        }

        $this->entityManager->flush();
    }

    public function dropLeavers(Table $table): void
    {
        $tableUsers = $table->getTableUsers()->filter(
            fn(TableUser $tableUser) =>  $tableUser->getLeaver()
        )->toArray();

        foreach ($tableUsers as $tableUser) {
            $this->entityManager->remove($tableUser);
        }
        $this->entityManager->flush();
    }

    public function setLoosePlayers(array $activePlayersSortedByPlace): void
    {
        // делаем тайм до цикла, чтобы время точно было одинаковое
        $time = time();
        /** @var TableUser $activePlayerSortedByPlace */
        foreach ($activePlayersSortedByPlace as $activePlayerSortedByPlace) {
            if ($activePlayerSortedByPlace->getStack() <= 0) {
                $activePlayerSortedByPlace
                    ->setStatus(TableUserStatus::Lose)
                    ->setUpdatedAt($time);
                $this->entityManager->persist($activePlayerSortedByPlace);
            }
        }
    }

    public function preparePlayersToNewRound(array $players): void
    {
        /** @var TableUser $player */
        foreach ($players as $player) {
            $betSum = Calculator::add($player->getBet(), $player->getBetSum());
            if (
                $player->getStack() <= 0
                && $player->getStatus()->value === TableUserStatus::Active->value
            ) {
                $player->setBetType(BetType::AllIn);
            }

            $player->setBet(0)
                ->setBetSum($betSum);
            $this->entityManager->persist($player);
        }

        $this->entityManager->flush();
    }

    public function excludeSilentPlayers(array $players): array
    {
        $activePlayers = [];
        foreach ($players as $place => $player) {
            if (
                $player->getBetType()?->value === BetType::AllIn->value
                || $player->getBetType()?->value === BetType::Fold->value
            ) {
                continue;
            }

            $activePlayers[$place] = $player;
        }

        return $activePlayers;
    }

    public function getPosition($place, $dealer, $places): string
    {
        $dealerPlaceIndex   = (int) array_search($dealer, $places);
        $placesAfterDealer  = array_slice($places, $dealerPlaceIndex);
        $placesBeforeDealer = array_slice($places, 0, $dealerPlaceIndex);
        $sortedPlaces       = [...$placesAfterDealer, ...$placesBeforeDealer];

        $indexPosition = array_search($place, $sortedPlaces);
        $countPlayers  = count($places);

        $position = $indexPosition === 0 ? PlayerPosition::Button->value : '';
        if ($countPlayers > 2) {
            $position = match ($indexPosition) {
                1       => PlayerPosition::SmallBlind->value,
                2       => PlayerPosition::BigBlind->value,
                default => $position,
            };

            if ($countPlayers < 7) {
                $position = match ($indexPosition) {
                    3       => PlayerPosition::UnderTheGun->value,
                    4       => PlayerPosition::MiddlePosition->value,
                    5       => PlayerPosition::CutOff->value,
                    default => $position,
                };
            }

            if ($countPlayers > 6 && $countPlayers < 10) {
                $position = match ($indexPosition) {
                    3, 4 => PlayerPosition::UnderTheGun->value,
                    5, 6, 7 => PlayerPosition::MiddlePosition->value,
                    8       => PlayerPosition::CutOff->value,
                    default => $position,
                };
            }

            if ($countPlayers === 10) {
                $position = match ($indexPosition) {
                    3, 4 => PlayerPosition::UnderTheGun->value,
                    5, 6, 7 => PlayerPosition::MiddlePosition->value,
                    8, 9 => PlayerPosition::CutOff->value,
                    default => $position,
                };
            }
        }

        if ($countPlayers < 3) {
            $position = match ($indexPosition) {
                0       => PlayerPosition::SmallBlind->value,
                1       => PlayerPosition::BigBlind->value,
                default => $position,
            };
        }

        return $position;
    }

    /**
     * @throws \Exception
     */
    public function dropAfk(Table $table): void
    {
        $afkPlayers = $this->tableUserRepository->getAfkPlayers();

        if (!$afkPlayers) {
            return;
        }

        /** @var TableUser  $player */
        foreach ($afkPlayers as $player) {
            if (!$player->getSeatOut()) {
                continue;
            }

            if ($player->getSeatOut() + $table->getReconnectTime() < time()) {
                ($this->returnRemainingsPlayerBalanceHandler)($player);
                $this->entityManager->remove($player);
            }
        }
        $this->entityManager->flush();
    }
}
