<?php

declare(strict_types=1);

namespace App\Response;

use App\ValueObject\Card;

class CardResponse
{
    public static function collection(Card ...$cards): array
    {
        $response = [];

        foreach ($cards as $card) {
            $response[] = [
                'suit'  => $card->getSuit()->name,
                'value' => $card->getView(),
            ];
        }

        return $response;
    }
}
