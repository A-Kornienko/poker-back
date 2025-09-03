<?php

declare(strict_types=1);

namespace App\ValueObject\TableHistory;

class BlindsTableHistory
{
    protected int $smallBlindPlace = 0;

    protected int $bigBlindPlace = 0;

    protected float $smallBlind = 0;

    protected float $bigBlind = 0;

    protected array $mapping = [
        'smallBlindPlace' => 'setSmallBlindPlace',
        'bigBlindPlace'   => 'setBigBlindPlace',
        'smallBlind'      => 'setSmallBlind',
        'bigBlind'        => 'setBigBlind',
    ];

    public function getSmallBlindPlace()
    {
        return $this->smallBlindPlace;
    }

    public function setSmallBlindPlace($smallBlindPlace)
    {
        $this->smallBlindPlace = $smallBlindPlace;

        return $this;
    }

    public function getBigBlindPlace()
    {
        return $this->bigBlindPlace;
    }

    public function setBigBlindPlace($bigBlindPlace)
    {
        $this->bigBlindPlace = $bigBlindPlace;

        return $this;
    }

    public function getSmallBlind()
    {
        return $this->smallBlind;
    }

    public function setSmallBlind($smallBlind)
    {
        $this->smallBlind = $smallBlind;

        return $this;
    }

    public function getBigBlind()
    {
        return $this->bigBlind;
    }

    public function setBigBlind($bigBlind)
    {
        $this->bigBlind = $bigBlind;

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
            'smallBlindPlace' => $this->smallBlindPlace,
            'bigBlindPlace'   => $this->bigBlindPlace,
            'smallBlind'      => $this->smallBlind,
            'bigBlind'        => $this->bigBlind
        ];
    }
}
