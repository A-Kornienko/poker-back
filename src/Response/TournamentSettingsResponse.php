<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\TournamentSetting;
use Symfony\Contracts\Translation\TranslatorInterface;

class TournamentSettingsResponse
{
    public static function details(TournamentSetting $tournamentSetting, ?TranslatorInterface $translator = null): array
    {
        return [
            'id'                   => $tournamentSetting->getId(),
            'entrySum'             => $tournamentSetting->getEntrySum(),
            'entryChips'           => $tournamentSetting->getEntryChips(),
            'startCountPlayers'    => $tournamentSetting->getStartCountPlayers(),
            'breakSettings'        => $tournamentSetting->getBreakSettings(),
            'buyInSettings'        => $tournamentSetting->getBuyInSettings(),
            'buyInType'            => $translator?->trans('tournament.buy_in_type_name.' . $tournamentSetting->getType()->name),
            'rule'                 => $translator?->trans('tournament.rule_name.' . $tournamentSetting->getRule()->name),
            'limitMembers'         => $tournamentSetting->getLimitMembers(),
            'tableSynchronization' => $tournamentSetting->getTableSynchronization(),
            'rake'                 => $tournamentSetting->getRake(),
            'minCountMembers'      => $tournamentSetting->getMinCountMembers(),
            'turnTime'             => $tournamentSetting->getTurnTime(),
            'timeBank'             => $tournamentSetting->getTimeBank()->toArray(),
            'blindSetting'         => $tournamentSetting->getBlindSetting()->toArray(),
        ];
    }
}
