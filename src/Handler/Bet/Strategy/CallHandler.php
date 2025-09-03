<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, TableUser, User};
use App\Enum\BetType;
use App\Enum\RoundActionType;
use App\Event\TableHistory\PlayerActionEvent;
use App\Exception\ResponseException;
use App\Helper\{Calculator, ErrorCodeHelper};

class CallHandler extends AbstractBetStrategyHandler implements BetHandlerInterface
{
    public function isApplicable(BetType $betType): bool
    {
        return BetType::Call->value === $betType->value;
    }

    public function makeBet(TableUser $player, ?bool $auto = false): void
    {
        $maxBet   = $this->tableUserRepository->getMaxBet($player->getTable());
        $bigBlind = $player->getTable()->getSetting()->getBigBlind();
        $maxBet   = $maxBet < $bigBlind ? $bigBlind : $maxBet;

        if (Calculator::add($player->getStack(), $player->getBet()) < $maxBet) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::SMALL_CALL);
        }

        $diff  = Calculator::subtract($maxBet, $player->getBet());
        $stack = Calculator::subtract($player->getStack(), $diff);
        $bet   = Calculator::add($player->getBet(), $diff);
        $player->setStack($stack);
        $player->setBet($bet);
        $player->setBetType($stack <= 0 ? BetType::AllIn : BetType::Call);

        $player = $auto ? $this->playerService->setSeatOut($player) : $player->setSeatOut(null);

        $this->updatePlayer($player);

        $this->dispatcher->dispatch(
            new PlayerActionEvent(
                $player->getTable()->getSession(),
                $player->getUser()->getLogin(),
                $player->getTable()->getRound(),
                $player->getPlace(),
                RoundActionType::Bet,
                BetType::Call,
                $diff
            ),
            PlayerActionEvent::NAME
        );

        $this->handleTurn($player);
    }

    public static function getDefaultPriority(): int
    {
        return BetType::Call->priority();
    }

    public function __invoke(Table $table, User $user, float $amount = 0): void
    {
        $this->validateTurn($table, $user);
        $this->makeBet($this->player);
    }
}
