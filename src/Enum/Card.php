<?php

declare(strict_types=1);

namespace App\Enum;

enum Card: int
{
    public function view()
    {
        return match($this) {
            Card::Ace   => 'A',
            Card::King  => 'K',
            Card::Queen => 'Q',
            Card::Jack  => 'J',
            Card::Ten   => '10',
            Card::Nine  => '9',
            Card::Eight => '8',
            Card::Seven => '7',
            Card::Six   => '6',
            Card::Five  => '5',
            Card::Four  => '4',
            Card::Three => '3',
            Card::Two   => '2',
        };
    }
    case Ace   = 14;
    case King  = 13;
    case Queen = 12;
    case Jack  = 11;
    case Ten   = 10;
    case Nine  = 9;
    case Eight = 8;
    case Seven = 7;
    case Six   = 6;
    case Five  = 5;
    case Four  = 4;
    case Three = 3;
    case Two   = 2;
}
