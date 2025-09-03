<?php

declare(strict_types=1);

namespace App\Enum;

enum BetType: string
{
    use Trait\ToArray;

    public function priority(): int
    {
        return match ($this) {
            BetType::Fold  => 1,
            BetType::Check => 2,
            BetType::Call  => 3,
            BetType::AllIn => 4,
            BetType::Bet, BetType::Raise => 5,
            default => 0
        };
    }

    case SmallBlind = 'smallBlind';
    case BigBlind   = 'bigBlind';
    case Fold       = 'fold';
    case Check      = 'check';
    case Call       = 'call';
    case Bet        = 'bet';
    case Raise      = 'raise';
    case AllIn      = 'allin';
}
