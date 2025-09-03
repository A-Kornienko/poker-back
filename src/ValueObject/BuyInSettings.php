<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class BuyInSettings
{
    private float $sum = 0; // сумма докупки +

    private float $chips = 0; // сколько фишек ты получишь за 1 докупку ?

    private int $limitByNumberOfTimes = 0; // сколько раз можно докупаться

    private int $limitByChipsInPercent = 0; // +лимит текущего банка игрока за столом нельзя делать докупку если банк игрока составляет больше чем ?% от стартовых фишек

    private int $limitByTime = 0; // лимит по времени от начала турнира +

    private int $limitByCountPlayers = 0; // лимит докупок в зависимости от количества игроков +

    public function toArray(): array
    {
        return [
            'sum'                   => $this->sum,
            'chips'                 => $this->chips,
            'limitByNumberOfTimes'  => $this->limitByNumberOfTimes,
            'limitByChipsInPercent' => $this->limitByChipsInPercent,
            'limitByTime'           => $this->limitByTime,
            'limitByCountPlayers'   => $this->limitByCountPlayers,
        ];
    }

    public function fromArray(array $buyInSettings): static
    {
        $mappings = [
            'sum'                   => 'setSum',
            'chips'                 => 'setChips',
            'limitByNumberOfTimes'  => 'setLimitByNumberOfTimes',
            'limitByChipsInPercent' => 'setLimitByChipsInPercent',
            'limitByTime'           => 'setLimitByTime',
            'limitByCountPlayers'   => 'setLimitByCountPlayers',
        ];

        foreach ($mappings as $key => $method) {
            if (array_key_exists($key, $buyInSettings)) {
                $this->$method($buyInSettings[$key]);
            }
        }

        return $this;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function setSum(float $sum): static
    {
        $this->sum = $sum;

        return $this;
    }

    public function getChips(): float
    {
        return $this->chips;
    }

    public function setChips(float $chips): static
    {
        $this->chips = $chips;

        return $this;
    }

    public function getLimitByNumberOfTimes(): int
    {
        return $this->limitByNumberOfTimes;
    }

    public function setLimitByNumberOfTimes(int $limitByNumberOfTimes): static
    {
        $this->limitByNumberOfTimes = $limitByNumberOfTimes;

        return $this;
    }

    public function getLimitByChipsInPercent(): int
    {
        return $this->limitByChipsInPercent;
    }

    public function setLimitByChipsInPercent(int $limitByChipsInPercent): static
    {
        $this->limitByChipsInPercent = $limitByChipsInPercent;

        return $this;
    }

    public function getLimitByTime(): int
    {
        return $this->limitByTime;
    }

    public function setLimitByTime(int $limitByTime): static
    {
        $this->limitByTime = $limitByTime;

        return $this;
    }

    public function getLimitByCountPlayers(): int
    {
        return $this->limitByCountPlayers;
    }

    public function setLimitByCountPlayers(int $limitByCountPlayers): static
    {
        $this->limitByCountPlayers = $limitByCountPlayers;

        return $this;
    }

    public static function getAdminConfig()
    {
        return [
            'sum' => [
                'name'    => 'sum',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Sum of buy-in',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'chips' => [
                'name'    => 'chips',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Amount of chips',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'limitByNumberOfTimes' => [
                'name'    => 'chips',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Number of available buy-ins for tournament',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'limitByChipsInPercent' => [
                'name'    => 'limitByChipsInPercent',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Buy-in limit depending on the player\'s chips in %, allows player buy new chips if his sum of chips less then % from entry chips',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'limitByTime' => [
                'name'    => 'limitByTime',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Limit by time from the start of the tournament, in seconds',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
            'limitByCountPlayers' => [
                'name'    => 'limitByCountPlayers',
                'type'    => TextType::class,
                'options' => [
                    'label'      => 'Limit by active players on the tournament, allows player buy new chips if number of active players in the tournament less then this value',
                    'attr'       => ['class' => 'ms-3'],
                    'label_attr' => ['class' => 'ps-3']
                ]
            ],
        ];
    }
}
