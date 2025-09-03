<?php

declare(strict_types=1);

namespace App\Handler\Tournaments\Registration;

use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.tournamentRegistration')]
interface TournamentRegistrationInterface
{
    public function isApplicable(Tournament $tournament): bool;

    public function __invoke(Tournament $tournament, User $user): void;
}
