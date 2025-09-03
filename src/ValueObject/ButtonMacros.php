<?php

declare(strict_types=1);

namespace App\ValueObject;

class ButtonMacros
{
    private $preflop = [1 => 2, 2 => 4, 3 => 6, 4 => 8];

    private $postflop = [1 => 20, 2 => 40, 3 => 60, 4 => 100];

    public function fromArray($buttonMacros): static
    {
        $mappings = [
            'preflop'  => 'setPreflop',
            'postflop' => 'setPostflop',
        ];
        foreach ($mappings as $key => $method) {
            if (array_key_exists($key, $buttonMacros)) {
                $this->$method($buttonMacros[$key]);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'preflop'  => $this->preflop,
            'postflop' => $this->postflop,
        ];
    }

    public function getPreflop(): array
    {
        return $this->preflop;
    }

    public function setPreflop(array $preflop): static
    {
        $this->preflop = $preflop;

        return $this;
    }

    public function getPostflop(): array
    {
        return $this->postflop;
    }

    public function setPostflop(array $postflop): static
    {
        $this->postflop = $postflop;

        return $this;
    }
}
