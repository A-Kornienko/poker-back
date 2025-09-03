<?php

declare(strict_types=1);

namespace App\ValueObject\TableHistory;

use App\Enum\BetType;
use App\Enum\RoundActionType;

//enum RoundActionType: string
//{
//    case Bet = 'bet';
//    case TimeBank = 'timeBank';
//}

class RoundActionTableHistory
{
    protected string $login = '';

    protected int $place;

    protected RoundActionType $type;

    protected ?BetType $betType = null;

    protected ?float $amount = null;

    protected array $mapping = [
        'place'   => 'setPlace',
        'type'    => 'setType',
        'betType' => 'setBetType',
        'amount'  => 'setAmount',
    ];

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getType(): RoundActionType
    {
        return $this->type;
    }

    public function setType(RoundActionType|string $type): static
    {
        $this->type = is_string($type) ? RoundActionType::tryFrom($type) : $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

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
            'place'   => $this->place,
            'type'    => $this->type->value,
            'betType' => $this->betType->value,
            'amount'  => $this->amount,
        ];
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    public function getBetType()
    {
        return $this->betType;
    }

    public function setBetType(BetType|string $betType)
    {
        $this->betType = is_string($betType) ? BetType::tryFrom($betType) : $betType;

        return $this;
    }
}
