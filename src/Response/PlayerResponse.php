<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\TableUser;
use App\Entity\Tournament;
use App\Entity\TournamentPrize;
use App\Entity\TournamentUser;
use App\Enum\Round;
use App\Enum\TableState;
use App\Enum\TableType;
use App\Enum\TableUserStatus;
use App\Enum\TournamentStatus;
use App\Helper\Calculator;

class PlayerResponse
{
    /**
     * @param array $players
     * @return array
     */
    public static function tableStateCollection(array $players): array
    {
        $response = [];
        /** @var TableUser $player */
        foreach ($players as $player) {
            $response[$player->getPlace()]
                = static::tableStateItem($player);
        }

        return $response;
    }

    public static function tableStateItem(TableUser $player): array
    {
        $isShowCards = $player->getTable()->getState() === TableState::Finish->value
            && $player->getTable()->getRound()->value === Round::ShowDown->value
            && $player->getStatus()->value !== TableUserStatus::Pending->value;

        return [
            'profile' => [
                'name'   => $player->getUser()->getLogin(),
                'avatar' => $player->getUser()->getAvatar(),
            ],
            'tableId'    => $player->getTable()->getId(),
            'stack'      => $player->getStack() ?? 0,
            'place'      => $player->getPlace() ?? [],
            'betType'    => $player->getBetType()?->value,
            'betExpTime' => (int)$player->getBetExpirationTime() - time() < 0 ? 0 : (int)$player->getBetExpirationTime() - time(),
            'timeBank'   => static::getPlayerTimeBank($player),
            'bet'        => $player->getBet() ?? [],
            'status'     => $player->getStatus()?->value ?? [],
            'cards'      => $isShowCards ? CardResponse::collection(...$player->getCards()) : [],
            'afk'        => (bool) $player->getSeatOut(),
        ];
    }

    public static function tableCollection(array $players): array
    {
        $response = [];
        foreach ($players as $player) {
            $response[] = [
                // 'country' => $player->getUser()->getMainUser()->getUserInfo()['country'],
                'login' => $player->getUser()->getLogin(),
                'table' => $player->getTable()->getNumber(),
                'tableId' => $player->getTable()->getId(),
                'stack' => $player->getStack()
            ];
        }

        return $response;
    }

    public static function tournamentCollection(Tournament $tournament, array $tournamentUsers): array
    {
        $response = [];

        /** @var TournamentUser $tournamentUser */
        foreach ($tournamentUsers as $tournamentUser) {
            $currentTableUser = $tournamentUser->getUser()->getTableUsers()
                ->filter(
                    fn(TableUser $player) => $player->getUser()->getId() === $tournamentUser->getUser()->getId()
                        && $player->getTable()->getSetting()->getType() === TableType::Tournament
                        && $player->getTable()->getTournament()?->getId() === $tournament->getId()
                )
                ->first();

            $stack   = $currentTableUser ? $currentTableUser->getStack() : $tournament->getSetting()->getEntryChips();
            $stackBB = $currentTableUser ? 
                Calculator::subtract($stack, $tournament->getBigBlind()) : 
                Calculator::subtract($tournament->getSetting()->getEntryChips(), $tournament->getBigBlind());

            // Добавляем ранг игрока и приз, если турнир завершён
            $rank        = 0;
            $prizeAmount = 0;
            if ($tournament->getStatus() === TournamentStatus::Finished) {
                $rank = $tournament->getTournamentUsers()->filter(
                    fn(TournamentUser $tu) => $tu->getUser()->getId() === $tournamentUser->getUser()->getId()
                )->first()?->getRank();

                $prizeAmount = $tournament->getPrizes()->filter(
                    fn(TournamentPrize $prize) => $prize->getWinner()?->getId() === $tournamentUser->getUser()->getId()
                )->first()?->getSum() ?? 0;
            }

            $response[] = [
                'login'   => $tournamentUser->getUser()->getLogin(),
                'stack'   => $stack,
                'stackBB' => $stackBB,
                'rank'    => $rank,
                'prize'   => $prizeAmount,
                'tableId' => $tournamentUser?->getTable()?->getId()
            ];
        }

        return $response;
    }

    public static function getPlayerTimeBank(TableUser $player): array
    {
        $table          = $player->getTable();
        $playerTimeBank = $player->getTimeBank();
        if (!$table->getSetting()->getTimeBank()->getTime()) {
            $playerTimeBank->setActive(false);

            return [];
        }

        if (!$playerTimeBank->getTime() && !$playerTimeBank->getActivationTime()) {
            $playerTimeBank->setTime($table->getSetting()->getTimeBank()->getTime());

            return $playerTimeBank->toArray();
        }

        return $playerTimeBank->toArray();
    }
}
