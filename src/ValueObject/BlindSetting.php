<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class BlindSetting
{
    private int $blindSpeed = 0; // скорость обновления блайндов в секунду

    private float $blindCoefficient = 0; // коэффициент увелечения блайндов

    private float $smallBlind = 0; // стартовая сумма малого блайнда

    private float $bigBlind = 0; // стартовая сумма большого блайнда

    public function toArray(): array
    {
        return [
            'blindSpeed'       => $this->blindSpeed,
            'blindCoefficient' => $this->blindCoefficient,
            'smallBlind'       => $this->smallBlind,
            'bigBlind'         => $this->bigBlind,
        ];
    }

    public function fromArray(array $blindSettings): static
    {
        $mappings = [
            'blindSpeed'       => 'setBlindSpeed',
            'blindCoefficient' => 'setBlindCoefficient',
            'smallBlind'       => 'setSmallBlind',
            'bigBlind'         => 'setBigBlind',
        ];

        foreach ($mappings as $key => $method) {
            if (array_key_exists($key, $blindSettings)) {
                $this->$method($blindSettings[$key]);
            }
        }

        return $this;
    }

    public function getBlindSpeed(): int
    {
        return $this->blindSpeed;
    }

    public function setBlindSpeed(int $blindSpeed): static
    {
        $this->blindSpeed = $blindSpeed;

        return $this;
    }

    public function getBlindCoefficient(): float
    {
        return $this->blindCoefficient;
    }

    public function setBlindCoefficient(float $blindCoefficient): static
    {
        $this->blindCoefficient = $blindCoefficient;

        return $this;
    }

    public function getSmallBlind(): float
    {
        return $this->smallBlind;
    }

    public function setSmallBlind(float $smallBlind): static
    {
        $this->smallBlind = $smallBlind;

        return $this;
    }

    public function getBigBlind(): float
    {
        return $this->bigBlind;
    }

    public function setBigBlind(float $bigBlind): static
    {
        $this->bigBlind = $bigBlind;

        return $this;
    }

    public static function getAdminConfig(): array
    {
        return [
            'blindSpeed' => [
                'name'    => 'blindSpeed',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Blind speed',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'blindCoefficient' => [
                'name'    => 'blindCoefficient',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Blind coefficient',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'smallBlind' => [
                'name'    => 'smallBlind',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Small Blind',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'bigBlind' => [
                'name'    => 'bigBlind',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Big Blind',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ]
        ];
    }
}
