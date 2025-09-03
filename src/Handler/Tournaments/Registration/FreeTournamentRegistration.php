<?php

declare(strict_types=1);

namespace App\Handler\Tournaments\Registration;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentType;

class FreeTournamentRegistration extends AbstractTournamentRegistration implements TournamentRegistrationInterface
{
    public function isApplicable(Tournament $tournament): bool
    {
        return $tournament->getSetting()->getType() === TournamentType::Free;
    }

    public function __invoke(Tournament $tournament, User $user): void
    {
        $this->validateDefaultRegistration($tournament, $user);
        $this->createPlayer($tournament, $user);
    }
}
