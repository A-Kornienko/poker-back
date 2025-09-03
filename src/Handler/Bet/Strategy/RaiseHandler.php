<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, User};
use App\Enum\{BetType, RoundActionType};
use App\Event\TableHistory\PlayerActionEvent;
use App\Exception\ResponseException;
use App\Helper\{Calculator, ErrorCodeHelper};

class RaiseHandler extends AbstractBetStrategyHandler implements BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool
    {
        return BetType::Raise->value === $betType->value || BetType::Bet->value === $betType->value;
    }

    public static function getDefaultPriority(): int
    {
        return BetType::Bet->priority();
    }

    public function __invoke(Table $table, User $user, float $amount = 0): void
    {
        $this->validateTurn($table, $user);

        $maxBet = $this->tableUserRepository->getMaxBetExcludeCurrentUser($table, $user);
        $maxBet = $maxBet < $table->getBigBlind() ? $table->getBigBlind() : $maxBet;
        $bet    = Calculator::add(0, $amount);
        $sumStack = Calculator::add($this->player->getStack(), $this->player->getBet());
        // Если недостаточно средств
        if ($amount > $sumStack) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::BIG_BET);
        }

        if ($bet < $maxBet && $sumStack >= $maxBet) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::SMALL_BET);
        }

        $stack = Calculator::subtract($sumStack, $amount);
        $this->player->setStack($stack)
            ->setBet($bet)
            ->setSeatOut(null);

        match (true) {
            $this->player->getStack() <= 0 => $this->player->setBetType(BetType::AllIn),
            $bet > $maxBet                 => $this->player->setBetType(BetType::Raise),
            $bet === $maxBet               => $this->player->setBetType(BetType::Call),
            default                        => $this->player->setBetType(BetType::Bet),
        };

        $this->updatePlayer($this->player);

        $this->dispatcher->dispatch(
            new PlayerActionEvent(
                $table->getSession(),
                $this->player->getUser()->getLogin(),
                $table->getRound(),
                $this->player->getPlace(),
                RoundActionType::Bet,
                BetType::Raise,
                $amount
            ),
            PlayerActionEvent::NAME
        );

        $this->handleTurn($this->player);
    }
}
