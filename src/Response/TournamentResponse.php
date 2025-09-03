<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Tournament;
use App\Entity\TournamentPrize;
use App\Entity\TournamentUser;
use App\Entity\User;
use App\Enum\TournamentType;
use App\Helper\DateTimeHelper;
use App\Service\TournamentService;
use Symfony\Contracts\Translation\TranslatorInterface;

class TournamentResponse
{
    public static function collection(
        ?TranslatorInterface $translator = null,
        Tournament ...$tournaments
    ): array {
        $response = [];
        foreach ($tournaments as $tournament) {
            $response[] = static::item($tournament, $translator);
        }

        return $response;
    }

    public static function item(Tournament $tournament, ?TranslatorInterface $translator): array
    {
        return [
            // если до турнира меньше часа, то показываем минуты, а иначе дату начала
            'tournamentId' => $tournament->getId(),
            'start'        => ($tournament->getDateStart() - time() <= 3600 && $tournament->getDateStart() - time() > 0)
                ? $translator->trans('time_until_start', ['%minutes%' => round(($tournament->getDateStart() - time()) / 60)])
                : ($tournament->getDateStart() - time() > 0
                    ? DateTimeHelper::formatted($tournament->getDateStart())
                    : $translator->trans('tournament has started')),
            'name'      => $tournament->getName(),
            'buyIn'     => ($tournament->getSetting()->getType() === TournamentType::Paid) ? $tournament->getSetting()->getEntrySum() : $translator?->trans('tournament.buy_in_type_name.' . $tournament->getSetting()->getType()->name),
            'members'   => $tournament->getTournamentUsers()->count(),
            'prizeFund' => $tournament->getPrizes()->reduce(fn(int $carry, TournamentPrize $prize) => $carry + $prize->getSum(), 0),
        ];
    }

    public static function details(Tournament $tournament, ?User $user = null, ?TranslatorInterface $translator = null): array
    {
        $isRegistered = $user && $tournament->getTournamentUsers()
            ->map(fn(TournamentUser $tournamentUser) => $tournamentUser->getUser())
            ->contains($user);

        return [
            'id'          => $tournament->getId(),
            'name'        => $tournament->getName(),
            'description' => $tournament->getDescription() ?? "",
            'dateStart'   => ($tournament->getDateStart() - time() <= 3600 && $tournament->getDateStart() - time() > 0)
            ? $translator->trans('time_until_start', ['%minutes%' => round(($tournament->getDateStart() - time()) / 60)])
            : ($tournament->getDateStart() - time() > 0
                ? DateTimeHelper::formatted($tournament->getDateStart())
                : $translator->trans('tournament has started')),
            'smallBlind'                => $tournament->getSmallBlind(),
            'bigBlind'                  => $tournament->getBigBlind(),
            'dateStartRegistration'     => DateTimeHelper::formatted($tournament->getDateStartRegistration()),
            'dateEndRegistration'       => DateTimeHelper::formatted($tournament->getDateEndRegistration()),
            'isRegistered'              => $isRegistered,
            'prizeFund'                 => $tournament->getPrizes()->reduce(fn(int $carry, TournamentPrize $prize) => $carry + $prize->getSum(), 0),
            'members'                   => $tournament->getTournamentUsers()->map(fn(TournamentUser $tournamentUser) => $tournamentUser->getUser()->getLogin()),
            'image'                     => $tournament->getImage(),
            'isDeniedRegistration'      => TournamentService::isDeniedRegistration($tournament),
            'isOpenRegistration'        => $tournament->getDateEndRegistration() >= time(),
            'status'                    => $translator?->trans('tournament.status.' . $tournament->getStatus()->name),
            'balance'                   => $tournament->getBalance(),
            'remainingRegistrationTime' => round(($tournament->getDateEndRegistration() - time()) / 60),
            'setting'                   => TournamentSettingsResponse::details($tournament->getSetting(), $translator),
        ];
    }

    public static function shortInfo(Tournament $tournament, ?TranslatorInterface $translator = null, User $user = null): array
    {
        return [
            'isRegistered' => $user && $tournament->getTournamentUsers()->map(fn(TournamentUser $tournamentUser) => $tournamentUser->getUser())->contains($user),
            'title'        => $tournament->getName(),
            'start'        => ($tournament->getDateStart() - time() <= 3600 && $tournament->getDateStart() - time() > 0)
                ? $translator->trans('time_until_start', ['%minutes%' => round(($tournament->getDateStart() - time()) / 60)])
                : ($tournament->getDateStart() - time() > 0
                    ? DateTimeHelper::formatted($tournament->getDateStart())
                    : $translator->trans('tournament has started')),
            'finishRegistrationTime' => DateTimeHelper::formatted($tournament->getDateEndRegistration()),
            'buyInType'              => $translator?->trans('tournament.buy_in_type_name.' . $tournament->getSetting()->getType()->name),
            'buyIn'                  => $tournament->getSetting()->getBuyInSettings()->getSum(),
            'prizeFund'              => $tournament->getPrizes()->reduce(fn(int $carry, TournamentPrize $prize) => $carry + $prize->getSum(), 0),
        ];
    }
}
