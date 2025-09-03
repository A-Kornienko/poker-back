<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Enum\StackViewCurrency;

class StackView
{
    private bool $active = false;

    //current currency from setting
    private StackViewCurrency $currency = StackViewCurrency::Dollar;

    //current value for stack view
    private StackViewCurrency $value = StackViewCurrency::Dollar;

    public function fromArray($stackView): static
    {
        $mappings = [
            'active'   => 'setActive',
            'currency' => 'setCurrency',
            'value'    => 'setValue',
        ];

        if (array_key_exists('value', $stackView)) {
            $stackView['value'] = StackViewCurrency::tryFrom($stackView['value']['value']);
        }

        if (array_key_exists('currency', $stackView)) {
            $stackView['currency'] = StackViewCurrency::tryFrom($stackView['currency']['value']);
        }

        foreach ($mappings as $key => $method) {
            if (array_key_exists($key, $stackView)) {
                $this->$method($stackView[$key]);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'active'   => $this->getActive(),
            'currency' => $this->getCurrency()->asArray(),
            'value'    => $this->getValue()->asArray(),
        ];
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getValue(): StackViewCurrency
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getCurrency(): StackViewCurrency
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }
}
