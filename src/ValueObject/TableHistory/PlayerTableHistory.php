<?php

declare(strict_types=1);

namespace App\ValueObject\TableHistory;

class PlayerTableHistory
{
    protected array $mapping = [
        'place'      => 'setPlace',
        'login'      => 'setLogin',
        'cards'      => 'setCards',
        'stack'      => 'setStack',
        'position'   => 'setPosition',
        'isMyPlayer' => 'setIsMyPlayer',
    ];

    private int $place = 0;

    private string $login = '';

    private array $cards = [];

    private float $stack = 0;

    private string $position = '';

    private bool $isMyPlayer = false;

    public function getPlace(): int
    {
        return $this->place;
    }

    public function setPlace(int $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function setCards(array $cards): static
    {
        $this->cards = $cards;

        return $this;
    }

    public function getStack(): float
    {
        return $this->stack;
    }

    public function setStack(float $stack): static
    {
        $this->stack = $stack;

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getIsMyPlayer(): bool
    {
        return $this->isMyPlayer;
    }

    public function setIsMyPlayer(bool $isMyPlayer): static
    {
        $this->isMyPlayer = $isMyPlayer;

        return $this;
    }

    public function fromArray(array $blindsTableHistory): static
    {
        foreach ($blindsTableHistory as $key => $value) {
            if (array_key_exists($key, $this->mapping)) {
                $method = $this->mapping[$key];
                $this->$method($value);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'place'      => $this->place,
            'login'      => $this->login,
            'cards'      => $this->cards,
            'stack'      => $this->stack,
            'position'   => $this->position,
            'isMyPlayer' => $this->isMyPlayer,
        ];
    }
}
