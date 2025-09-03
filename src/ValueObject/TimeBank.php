<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class TimeBank
{
    protected int $time = 0;

    protected int $timeLimit = 60;

    protected int $periodInSec = 0;

    protected int $periodInHand = 0;

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getTimeLimit(): int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function getPeriodInSec(): int
    {
        return $this->periodInSec;
    }

    public function setPeriodInSec(int $period): static
    {
        $this->periodInSec = $period;

        return $this;
    }

    public function getPeriodInHand(): int
    {
        return $this->periodInHand;
    }

    public function setPeriodInHand(int $periodInHand): static
    {
        $this->periodInHand = $periodInHand;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'time'         => $this->time,
            'timeLimit'    => $this->timeLimit,
            'periodInSec'  => $this->periodInSec,
            'periodInHand' => $this->periodInHand
        ];
    }

    public function fromArray(?array $timeBank): static
    {
        if (!$timeBank) {
            return $this;
        }

        if (array_key_exists('time', $timeBank)) {
            $this->setTime($timeBank['time']);
        }

        if (array_key_exists('timeLimit', $timeBank)) {
            $this->setTimeLimit($timeBank['timeLimit']);
        }

        if (array_key_exists('periodInSec', $timeBank)) {
            $this->setPeriodInSec($timeBank['periodInSec']);
        }

        if (array_key_exists('periodInHand', $timeBank)) {
            $this->setPeriodInHand($timeBank['periodInHand']);
        }

        return $this;
    }

    public static function getAdminConfig(): array
    {
        return [
            'time' => ['name' => 'time', 'type' => TextType::class,
                'options'     => [
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'timeLimit' => ['name' => 'timeLimit', 'type' => TextType::class,
                'options'          => [
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'periodInSec' => ['name' => 'periodInSec', 'type' => TextType::class,
                'options'            => [
                    'label'      => 'Period in seconds',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'periodInHand' => ['name' => 'periodInHand', 'type' => TextType::class,
                'options'             => [
                    'label'      => 'Period in hand',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ]
        ];
    }
}
