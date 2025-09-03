<?php

declare(strict_types=1);

namespace App\Handler\Cards;

use App\Enum\Card as CardValue;
use App\Enum\CardSuit;
use App\ValueObject\Card;

abstract class AbstractCardsHandler
{
    protected array $deck;

    public function __construct()
    {
        $this->createDeck();
    }

    public function getCards(int $count = 2, Card ...$excludeCards): array
    {
        if (count($excludeCards) > 0) {
            foreach ($excludeCards as $card) {
                unset($this->deck[$card->getValue() . $card->getSuit()->value]);
            }
        }

        return array_slice($this->deck, 0, $count);
    }

    protected function createDeck(): void
    {
        foreach (CardSuit::cases() as $suit) {
            foreach (CardValue::cases() as $card) {
                $this->deck[$card->value . $suit->value] = (new Card())
                    ->setName($card->name)
                    ->setValue((int) $card->value)
                    ->setView($card->view())
                    ->setSuit($suit);
            }
        }

        uksort($this->deck, fn() => rand() > rand());
        uksort($this->deck, fn() => rand() > rand());
        uksort($this->deck, fn() => rand() > rand());

        $halfDeck   = count($this->deck) / 2;
        $firstPart  = array_slice($this->deck, 0, $halfDeck);
        $secondPart = array_slice($this->deck, -$halfDeck);
        $this->deck = [...$secondPart, ...$firstPart];
    }
}
