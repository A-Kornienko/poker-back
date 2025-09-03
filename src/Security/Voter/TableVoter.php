<?php

namespace App\Security\Voter;

use App\Entity\Table;
use App\Entity\TableSetting;
use App\Entity\TableUser;
use App\Entity\User;
use App\Enum\TableType;
use App\Enum\TableUserStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TableVoter extends Voter
{
    public const CONNECT_TO_TABLE           = 'connectToTable';
    public const CHECK_PLAYER_PARTICIPATION = 'checkPlayerParticipation';
    public const IS_CASH_TABLE              = 'isCashTable';
    public const IS_LOSER                   = 'isLoser';

    public function __construct(
        protected readonly Security $security
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return match($attribute) {
            self::CONNECT_TO_TABLE           => $this->canConnectToTable($subject),
            self::CHECK_PLAYER_PARTICIPATION => $this->checkPlayerParticipation($subject),
            self::IS_CASH_TABLE              => $this->isCashTable($subject),
            self::IS_LOSER                   => $this->isLoser($subject),
            default                          => false
        };
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }

    protected function canConnectToTable(TableSetting $tableSetting): bool
    {
        return ! ($tableSetting->getType() === TableType::Tournament);
    }

    protected function checkPlayerParticipation(Table $table): bool
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        return $user && $table->getTableUsers()->exists(fn($key, $tableUser): bool => $tableUser->getUser()->getId() === $user?->getId());
    }

    protected function isCashTable(Table $table): bool
    {
        return !$table->getTournament();
    }

    protected function isLoser(Table $table): bool
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        return (bool) $table->getTableUsers()
            ->filter(fn($player) => $player->getUser()->getId() === $user?->getId() && $player->getStatus()->value === TableUserStatus::Lose->value)->count();
    }
}
