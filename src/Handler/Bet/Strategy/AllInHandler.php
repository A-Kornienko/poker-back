<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, User};
use App\Enum\{BetType, RoundActionType};
use App\Event\TableHistory\PlayerActionEvent;
use App\Helper\{Calculator};

class AllInHandler extends AbstractBetStrategyHandler implements BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool
    {
        return BetType::AllIn->value === $betType->value;
    }

    public static function getDefaultPriority(): int
    {
        return BetType::AllIn->priority();
    }

    public function __invoke(Table $table, User $user, float $amount = 0): void
    {
        $this->validateTurn($table, $user);

        $bet = Calculator::add($this->player->getBet(), $this->player->getStack());

        $this->player->setStack(0);
        $this->player->setBet($bet);
        $this->player->setBetType(BetType::AllIn);
        $this->player->setSeatOut(null);

        $this->updatePlayer($this->player);
        $this->dispatcher->dispatch(
            new PlayerActionEvent(
                $table->getSession(),
                $this->player->getUser()->getLogin(),
                $table->getRound(),
                $this->player->getPlace(),
                RoundActionType::Bet,
                BetType::AllIn,
                $bet
            ),
            PlayerActionEvent::NAME
        );
        $this->handleTurn($this->player);
    }
}
