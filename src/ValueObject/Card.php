<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\{CardSuit, CardType};

class Card
{
    private string $name;

    private CardSuit $suit;

    private int $value;

    private CardType $type;

    private string $view;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSuit(): CardSuit
    {
        return $this->suit;
    }

    public function setSuit(CardSuit $suit): static
    {
        $this->suit = $suit;

        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return CardType
     */
    public function getType(): CardType
    {
        return $this->type;
    }

    /**
     * @param CardType|null $type
     * @return $this
     */
    public function setType(?CardType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function fromArray(array $card): static
    {
        return $this->setValue($card['value'])
            ->setType(CardType::tryFrom($card['type']))
            ->setName($card['name'])
            ->setSuit(CardSuit::tryFrom($card['suit']))
            ->setView($card['view']);
    }

    public function toArray(): array
    {
        return [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
            'suit'  => $this->getSuit()->value,
            'view'  => $this->getView(),
            'type'  => $this->getType()->value,
        ];
    }

    public function __toString(): string
    {
        return $this->name . $this->suit->value . $this->type->value . $this->value . $this->view;
    }
}
