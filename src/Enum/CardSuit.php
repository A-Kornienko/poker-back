<?php

declare(strict_types=1);

namespace App\Enum;

enum CardSuit: string
{
    case Diamond = 'Diamond'; // Бубны
    case Club    = 'Club'; // Трефы
    case Heart   = 'Heart'; // Черви
    case Spade   = 'Spade'; // Пики
}
