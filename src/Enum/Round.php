<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Trait\ToArray;

enum Round: string
{
    use ToArray;

    public function next(): self
    {
        return match($this) {
            Round::PreFlop => Round::Flop,
            Round::Flop    => Round::Turn,
            Round::Turn    => Round::River,
            Round::River   => Round::ShowDown,
            default        => $this,
        };
    }

    public function countTableCards(): int
    {
        return match($this) {
            Round::Flop => 3,
            Round::River, Round::Turn => 1,
            default => 0
        };
    }

    case PreFlop    = 'preFlop';
    case Flop       = 'flop';
    case Turn       = 'turn';
    case River      = 'river';
    case ShowDown   = 'showDown';
    case FastFinish = 'fastFinish';
}
