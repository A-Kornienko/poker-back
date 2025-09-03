<?php

declare(strict_types=1);

namespace App\Event\TableHistory;

use App\Enum\BetType;
use App\Enum\Round;
use App\Enum\RoundActionType;
use App\Event\AbstractEvent;

class PlayerActionEvent extends AbstractEvent
{
    public const NAME = 'PlayerAction';

    public function __construct(
        protected string $session,
        protected string $login,
        protected Round $round,
        protected int $place,
        protected RoundActionType $actionType,
        protected ?BetType $betType = null,
        protected ?float $amount = null,
    ) {
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function getActionType()
    {
        return $this->actionType;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getRound()
    {
        return $this->round;
    }

    public function getBetType()
    {
        return $this->betType;
    }
}
