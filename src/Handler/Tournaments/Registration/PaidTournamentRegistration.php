<?php

declare(strict_types=1);

namespace App\Handler\Tournaments\Registration;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentType;
use App\Handler\Balance\BuyInTournamentHandler;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaidTournamentRegistration extends AbstractTournamentRegistration implements TournamentRegistrationInterface
{
   public function __construct(
       EntityManagerInterface $entityManager,
       TournamentService $tournamentService,
       TranslatorInterface $translator,
       protected BuyInTournamentHandler $buyInTournamentHandler
   ) {
       parent::__construct($entityManager, $tournamentService, $translator);
   }

    public function isApplicable(Tournament $tournament): bool
    {
        return $tournament->getSetting()->getType() === TournamentType::Paid;
    }

    public function __invoke(Tournament $tournament, User $user): void
    {
        $this->validateDefaultRegistration($tournament, $user);

        $this->validateUsdBalance($tournament, $user);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            ($this->buyInTournamentHandler)($tournament, $user);
            $this->createPlayer($tournament, $user);

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }
}
