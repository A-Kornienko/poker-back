<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class BreakSettings
{
    protected int $period = 0; // Периодичность перерывов в секундах, вывод например раз в 15 минут/ 1 час

    protected int $lastTime = 0; // Врямя последнего перерыва в секундах, вывод например 12:30

    protected int $duration = 0; // Длительность в секундах, вывод например 15 минут/ 1 час

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function setPeriod(int $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getLastTime(): int
    {
        return $this->lastTime;
    }

    public function setLastTime(int $lastTime): static
    {
        $this->lastTime = $lastTime;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'period'   => $this->period,
            'lastTime' => $this->lastTime,
            'duration' => $this->duration
        ];
    }

    public function fromArray(?array $breakSettings): static
    {
        if (!$breakSettings) {
            return $this;
        }

        if (array_key_exists('period', $breakSettings)) {
            $this->setPeriod($breakSettings['period']);
        }

        if (array_key_exists('lastTime', $breakSettings)) {
            $this->setLastTime($breakSettings['lastTime']);
        }

        if (array_key_exists('duration', $breakSettings)) {
            $this->setDuration($breakSettings['duration']);
        }

        return $this;
    }

    public static function getAdminConfig(): array
    {
        return [
            'period' => ['name' => 'period', 'type' => TextType::class,
                'options'       => [
                    'label'      => 'Period in seconds',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            // 'lastTime' => ['name' => 'lastTime', 'type' => TextType::class,
            //     'options'         => [
            //         'attr'       => ['class' => 'ms-3'],
            //         'label_attr' => ['class' => 'ps-3']
            //     ]
            // ],
            'duration' => ['name' => 'duration', 'type' => TextType::class,
                'options'         => [
                    'label'      => 'Duration in seconds',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ]
        ];
    }
}
