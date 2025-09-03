<?php

declare(strict_types=1);

namespace App\ValueObject;

class Notification
{
    protected array $data = [];

    protected string $template;

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $twig): static
    {
        $this->template = $twig;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function addData(string $key, mixed $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }
}
