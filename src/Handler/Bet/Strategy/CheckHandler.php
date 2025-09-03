<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, TableUser, User};
use App\Enum\{BetType, RoundActionType};
use App\Event\TableHistory\PlayerActionEvent;
use App\Exception\ResponseException;
use App\Helper\{ErrorCodeHelper};

class CheckHandler extends AbstractBetStrategyHandler implements BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool
    {
        return BetType::Check->value === $betType->value;
    }

    public function makeBet(TableUser $player, ?bool $auto = false): void
    {
        $player->setBetType(BetType::Check);
        $player = $auto ? $this->playerService->setSeatOut($player) : $player->setSeatOut(null);

        $this->updatePlayer($player);

        $this->dispatcher->dispatch(
            new PlayerActionEvent(
                $player->getTable()->getSession(),
                $player->getUser()->getLogin(),
                $player->getTable()->getRound(),
                $player->getPlace(),
                RoundActionType::Bet,
                BetType::Check,
            ),
            PlayerActionEvent::NAME
        );

        $this->handleTurn($player);
    }

    public static function getDefaultPriority(): int
    {
        return BetType::Check->priority();
    }

    public function __invoke(Table $table, User $user, float $amount = 0): void
    {
        $this->validateTurn($table, $user);

        $maxBet = $this->tableUserRepository->getMaxBetExcludeCurrentUser($table, $user);
        if ($this->player->getBet() < $maxBet) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::SMALL_CHECK);
        }

        $this->makeBet($this->player);
    }
}
