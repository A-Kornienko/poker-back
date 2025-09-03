<?php

declare(strict_types=1);

namespace App\ValueObject\TableHistory;

use App\Enum\Round;

class PotTableHistory
{
    protected ?float $preFlop = null;

    protected ?float $flop = null;

    protected ?float $turn = null;

    protected ?float $river = null;

    protected array $mapping = [
        Round::PreFlop->value => 'setPreFlop',
        Round::Flop->value    => 'setFlop',
        Round::Turn->value    => 'setTurn',
        Round::River->value   => 'setRiver',
    ];

    public function fromArray(array $roundAction): static
    {
        foreach ($roundAction as $key => $value) {
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
            Round::PreFlop->value => $this->preFlop,
            Round::Flop->value    => $this->flop,
            Round::Turn->value    => $this->turn,
            Round::River->value   => $this->river,
        ];
    }

    public function getPreFlop()
    {
        return $this->preFlop;
    }

    public function setPreFlop($preFlop)
    {
        $this->preFlop = $preFlop;

        return $this;
    }

    public function getFlop()
    {
        return $this->flop;
    }

    public function setFlop($flop)
    {
        $this->flop = $flop;

        return $this;
    }

    public function getTurn()
    {
        return $this->turn;
    }

    public function setTurn($turn)
    {
        $this->turn = $turn;

        return $this;
    }

    public function getRiver()
    {
        return $this->river;
    }

    public function setRiver($river)
    {
        $this->river = $river;

        return $this;
    }
}
