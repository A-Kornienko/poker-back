<?php

declare(strict_types=1);

namespace App\ValueObject;

class Combination
{
    protected string $name; // Name of combination

    protected array $cards; // 5 cards of Card objects

    protected int $rank; // number of rank combination

    protected array $mapping = [
        'name'  => 'setName',
        'rank'  => 'setRank',
        'cards' => 'setCards'
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function setCards(array $cards): static
    {
        foreach ($cards as $card) {
            $this->cards[] = is_array($card) ? (new Card())->fromArray($card) : $card;
        }

        return $this;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setRank(int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function toArray(): array
    {
        $data         = [];
        $data['name'] = $this->getName();
        $data['rank'] = $this->getRank();

        foreach ($this->getCards() as $card) {
            $data['cards'][] = [
                'name'  => $card->getName(),
                'value' => $card->getValue(),
                'suit'  => $card->getSuit()->value,
                'view'  => $card->getView(),
                'type'  => $card->getType()->value,
            ];
        }

        return $data;
    }

    public function fromArray(array $combination): static
    {
        foreach ($combination as $key => $value) {
            if (array_key_exists($key, $this->mapping)) {
                $method = $this->mapping[$key];
                $this->$method($value);
            }
        }

        return $this;
    }
}
