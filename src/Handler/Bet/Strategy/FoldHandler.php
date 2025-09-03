<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, TableUser, User};
use App\Enum\{BetType, RoundActionType, TableUserStatus};
use App\Event\TableHistory\PlayerActionEvent;

class FoldHandler extends AbstractBetStrategyHandler implements BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool
    {
        return BetType::Fold->value === $betType->value;
    }

    public function makeBet(TableUser $player, ?bool $auto = false): void
    {
        $player
            ->setBetType(BetType::Fold)
            ->setStatus(TableUserStatus::Pending);

        $player = $auto ? $this->playerService->setSeatOut($player) : $player->setSeatOut(null);

        $this->updatePlayer($player);

        $this->dispatcher->dispatch(
            new PlayerActionEvent(
                $player->getTable()->getSession(),
                $player->getUser()->getLogin(),
                $player->getTable()->getRound(),
                $player->getPlace(),
                RoundActionType::Bet,
                BetType::Fold,
            ),
            PlayerActionEvent::NAME
        );

        $this->handleTurn($player);
    }

    public static function getDefaultPriority(): int
    {
        return BetType::Fold->priority();
    }

    public function __invoke(Table $table, User $user, float $amount = 0): void
    {
        $this->validateTurn($table, $user);
        $this->makeBet($this->player);
    }
}
