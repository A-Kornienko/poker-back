<?php

declare(strict_types=1);

namespace App\ValueObject;

class TableUserTimeBank
{
    protected int $time = 0;

    protected bool $active = false;

    protected int $countPlayedHand = 0;

    protected int $activationTime = 0;

    protected int $lastUpdatedTime = 0;

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCountPlayedHand(): ?int
    {
        return $this->countPlayedHand;
    }

    public function setCountPlayedHand(?int $countHand): static
    {
        $this->countPlayedHand = $countHand;

        return $this;
    }

    public function getActivationTime(): int
    {
        return $this->activationTime;
    }

    public function setActivationTime(?int $lastTime): static
    {
        $this->activationTime = $lastTime;

        return $this;
    }

    public function getLastUpdatedTime(): ?int
    {
        return $this->lastUpdatedTime;
    }

    public function setLastUpdatedTime(?int $lastUpdatedTime): static
    {
        $this->lastUpdatedTime = $lastUpdatedTime;

        return $this;
    }

    public function fromArray(?array $timeBank): static
    {
        if (!$timeBank) {
            return $this;
        }

        if (array_key_exists('time', $timeBank)) {
            $this->setTime($timeBank['time']);
        }

        if (array_key_exists('active', $timeBank)) {
            $this->setActive($timeBank['active']);
        }

        if (array_key_exists('countPlayedHand', $timeBank)) {
            $this->setCountPlayedHand($timeBank['countPlayedHand']);
        }

        if (array_key_exists('activationTime', $timeBank)) {
            $this->setActivationTime($timeBank['activationTime']);
        }

        if (array_key_exists('lastUpdatedTime', $timeBank)) {
            $this->setLastUpdatedTime($timeBank['lastUpdatedTime']);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'time'            => $this->time,
            'active'          => $this->active,
            'countPlayedHand' => $this->countPlayedHand,
            'activationTime'  => $this->activationTime,
            'lastUpdatedTime' => $this->lastUpdatedTime
        ];
    }
}
