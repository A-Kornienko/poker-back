<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Table, TableUser};
use App\Enum\BetType;
use App\Enum\TableState;
use App\Helper\Calculator;

class ButtonResolverService
{
    /**
     * @param Table $table
     * @param TableUser|null $tableUser
     * @param $userMaxBet
     * @param mixed $maxBetExcludeCurrentUser
     * @return array|string[]
     */
    public function getBetButtons(Table $table, ?TableUser $tableUser, $maxBetExcludeCurrentUser): array
    {
        if (!$tableUser) {
            return [];
        }

        return match (true) {
            !$this->isValidTableUserForButtons($table, $tableUser)                                    => [],
            $table->getState() !== TableState::Run->value                                             => [],
            $maxBetExcludeCurrentUser <= 0                                                            => [BetType::Fold->value, BetType::Check->value, BetType::Raise->value, BetType::AllIn->value],
            $maxBetExcludeCurrentUser == $tableUser->getBet()                                         => [BetType::Fold->value, BetType::Check->value, BetType::Raise->value, BetType::AllIn->value],
            $maxBetExcludeCurrentUser < Calculator::add($tableUser->getBet(), $tableUser->getStack()) => [BetType::Fold->value, BetType::Call->value, BetType::AllIn->value],
            $maxBetExcludeCurrentUser > Calculator::add($tableUser->getBet(), $tableUser->getStack()) => [BetType::Fold->value, BetType::AllIn->value],
            default                                                                                   => [BetType::Fold->value, BetType::Check->value, BetType::Call->value, BetType::Raise->value,BetType::AllIn->value],
        };
    }

    public function getBetRange(Table $table, ?TableUser $tableUser, ?float $maxBetExcludeCurrentUser): array
    {
        $buttonRange = [
            'min' => 0,
            'max' => 0,
        ];

        if (!$tableUser) {
            return $buttonRange;
        }

        $buttonRange['min'] = Calculator::subtract($maxBetExcludeCurrentUser, $tableUser->getBet());
        $buttonRange['min'] = $buttonRange['min'] <= $table->getBigBlind() ? $table->getBigBlind() : $buttonRange['min'];
        $buttonRange['max'] = $tableUser->getStack() + $tableUser->getBet();

        return $buttonRange;
    }

    protected function isValidTableUserForButtons(Table $table, ?TableUser $tableUser)
    {
        return match (true) {
            !$tableUser                                                                                     => false,
            $tableUser->getPlace() !== $table->getTurnPlace()                                               => false,
            in_array($tableUser->getBetType()?->value, [BetType::Fold->value, BetType::AllIn->value], true) => false,
            default                                                                                         => true,
        };
    }
}
