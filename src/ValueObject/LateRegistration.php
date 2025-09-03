<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class LateRegistration
{
    protected int $maxBlindLevel = 0;

    protected int $timeAfterStart = 0;


    public function toArray(): array
    {
        return [
            'maxBlindLevel'  => $this->maxBlindLevel,
            'timeAfterStart' => $this->timeAfterStart,
        ];
    }

    public function fromArray(?array $lateRegistration): static
    {
        if (!$lateRegistration) {
            return $this;
        }

        if (array_key_exists('maxBlindLevel', $lateRegistration)) {
            $this->setMaxBlindLevel($lateRegistration['maxBlindLevel']);
        }

        if (array_key_exists('timeAfterStart', $lateRegistration)) {
            $this->setTimeAfterStart($lateRegistration['timeAfterStart']);
        }

        return $this;
    }

    public static function getAdminConfig(): array
    {
        return [
            'maxBlindLevel' => ['name' => 'maxBlindLevel', 'type' => TextType::class,
                'options'     => [
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'timeAfterStart' => ['name' => 'timeAfterStart', 'type' => TextType::class,
                'options'          => [
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ]
        ];
    }

    public function getMaxBlindLevel(): int
    {
        return $this->maxBlindLevel;
    }

    public function setMaxBlindLevel(int $maxBlindLevel): static
    {
        $this->maxBlindLevel = $maxBlindLevel;

        return $this;
    }

    public function getTimeAfterStart(): int
    {
        return $this->timeAfterStart;
    }

    public function setTimeAfterStart(int $timeAfterStart): static
    {
        $this->timeAfterStart = $timeAfterStart;

        return $this;
    }
}
