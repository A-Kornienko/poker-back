<?php

declare(strict_types=1);

namespace App\ValueObject\TableHistory;

use App\ValueObject\Card;
use App\ValueObject\Combination;

class WinnerTableHistory
{
    protected string $login;

    protected ?Combination $combination = null;

    protected array $handCards = [];

    protected float $sum;

    protected array $mapping = [
        'login'       => 'setLogin',
        'combination' => 'setCombination',
        'handCards'   => 'setHandCards',
        'sum'         => 'setSum',
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
        $handCards = [];
        foreach ($this->handCards as $card) {
            $handCards[] = $card->toArray();
        }

        return [
            'login'       => $this->login,
            'combination' => $this->combination?->toArray(),
            'handCards'   => $handCards,
            'sum'         => $this->sum,
        ];
    }

    /**
     * Get the value of login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set the value of login
     *
     * @param mixed $login
     * @return  self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get the value of combination
     */
    public function getCombination()
    {
        return $this->combination;
    }

    /**
     * Set the value of combination
     *
     * @return  self
     */
    public function setCombination(Combination|array|null $combination)
    {
        $this->combination = is_array($combination) ? (new Combination())->fromArray($combination) : $combination;

        return $this;
    }

    /**
     * Get the value of handCards
     */
    public function getHandCards()
    {
        return $this->handCards;
    }

    /**
     * Set the value of handCards
     *
     * @return  self
     */
    public function setHandCards(array $handCards)
    {
        foreach ($handCards as $card) {
            $this->handCards[] = is_array($card) ? (new Card())->fromArray($card) : $card;
        }

        return $this;
    }

    /**
     * Get the value of sum
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set the value of sum
     *
     * @param mixed $sum
     * @return  self
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }
}
