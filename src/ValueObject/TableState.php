<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\{Bank, Table, TableUser};

class TableState
{
    protected Table $table;

    protected array $players = [];

    protected array $banks = [];

    protected array $cards = [
        'table'  => [],
        'player' => [],
    ];

    protected array $betNavigation = [];

    protected array $betRange = [
        'min' => 0,
        'max' => 0,
    ];

    protected float $maxBet = 0;

    protected int $myPlace = 0;

    protected int $spectators = 0;

    protected array $chat = [];

    protected array $history = [];

    protected array $myPrize = [
        'rank' => 0,
        'sum'  => 0
    ];

    protected string $suggestCombination = '';

    public function getMyPrize(): array
    {
        return $this->myPrize;
    }

    public function setMyPrize(array $myPrize): static
    {
        $this->myPrize = $myPrize;

        return $this;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setPlayers(TableUser ...$players)
    {
        $this->players = $players;

        return $this;
    }

    public function getBanks(): array
    {
        return $this->banks;
    }

    public function setBanks(Bank ...$banks): static
    {
        $this->banks = $banks;

        return $this;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function setCards(array $tableCards, ?array $playerCards = [])
    {
        $this->cards['table']  = $tableCards;
        $this->cards['player'] = $playerCards;

        return $this;
    }

    public function getBetNavigation(): array
    {
        return $this->betNavigation;
    }

    public function setBetNavigation(array $betNavigation): static
    {
        $this->betNavigation = $betNavigation;

        return $this;
    }

    public function getBetRange(): array
    {
        return $this->betRange;
    }

    public function setBetRange(float $min = 0, float $max = 0): static
    {
        $this->betRange = [
            'min' => $min,
            'max' => $max,
        ];

        return $this;
    }

    public function getMyPlace(): int
    {
        return $this->myPlace;
    }

    public function setMyPlace(int $myPlace): static
    {
        $this->myPlace = $myPlace;

        return $this;
    }

    public function getSpectators(): int
    {
        return $this->spectators;
    }

    public function setSpectators(int $spectators): static
    {
        $this->spectators = $spectators;

        return $this;
    }

    public function getChat(): array
    {
        return $this->chat;
    }

    public function setChat(array $chat): static
    {
        $this->chat = $chat;

        return $this;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function setHistory(array $history)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get the value of table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * Set the value of table
     *
     * @return  self
     */
    public function setTable(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the value of maxBet
     */
    public function getMaxBet(): float
    {
        return $this->maxBet;
    }

    /**
     * Set the value of maxBet
     *
     * @return  self
     */
    public function setMaxBet(float $maxBet): static
    {
        $this->maxBet = $maxBet;

        return $this;
    }

    public function getSuggestCombination(): string
    {
        return $this->suggestCombination;
    }

    public function setSuggestCombination(string $suggestCombination): static
    {
        $this->suggestCombination = $suggestCombination;

        return $this;
    }
}
