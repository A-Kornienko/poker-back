<?php

declare(strict_types=1);

namespace App\Handler\Tournaments\Registration;

use App\Entity\Tournament;
use App\Entity\User;
use App\Enum\TournamentStatus;
use App\Enum\TournamentType;
use App\Handler\Balance\BuyInTournamentHandler;
use App\Repository\TableRepository;
use App\Service\PlayerService;
use App\Service\TableService;
use App\Service\TableSettingService;
use App\Service\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LateRegistrationTournamentHandler extends AbstractTournamentRegistration implements TournamentRegistrationInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TournamentService $tournamentService,
        TranslatorInterface $translator,
        protected BuyInTournamentHandler $buyInTournamentHandler,
        protected TableRepository $tableRepository,
        protected TableSettingService $tableSettingService,
        protected TableService $tableService,
        protected PlayerService $playerService
    ) {
        parent::__construct($entityManager, $tournamentService, $translator);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Tournament $tournament, User $user): void
    {
        $this->validateLateRegistration($tournament, $user);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            if ($tournament->getSetting()->getType() === TournamentType::Paid) {
                $this->validateUsdBalance($tournament, $user);
                ($this->buyInTournamentHandler)($tournament, $user);
            }

            $tournamentPlayer = $this->createPlayer($tournament, $user);

            $table = $this->tableRepository->findTournamentTableByMinPlayers($tournament);

            if (!$table) {
                $tableSetting = $this->tableSettingService->createByTournament($tournament);
                $table = $this->tableService->createTournamentTable($tournament, $tableSetting);
            }

            $tournamentPlayer->setTable($table);
            $this->entityManager->persist($tournamentPlayer);
            $this->entityManager->flush();

            $places = $table->getFreePlaces();

            $this->playerService->create($table, $user, current($places), $tournament->getSetting()->getEntryChips());
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }

    public function isApplicable(Tournament $tournament): bool
    {
        return $tournament->getSetting()->getLateRegistration()->getTimeAfterStart() || $tournament->getSetting()
                ->getLateRegistration()
                ->getMaxBlindLevel() &&
            !in_array(
                $tournament->getStatus(),
                [TournamentStatus::Pending, TournamentStatus::Finished, TournamentStatus::Canceled]
            );
    }
}
