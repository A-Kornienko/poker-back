<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Entity\Table;
use App\Entity\User;
use App\Enum\BankStatus;
use App\Enum\Round;
use App\Handler\AbstractHandler;
use App\Handler\Cards\Combination\SuggestPlayerCombinationHandler;
use App\Repository\SpectatorRepository;
use App\Repository\TableRepository;
use App\Repository\TableUserRepository;
use App\Repository\TournamentPrizeRepository;
use App\Repository\TournamentUserRepository;
use App\Response\TableStateResponse;
use App\Service\BankService;
use App\Service\ButtonResolverService;
use App\ValueObject\TableState;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTableStateHandler extends AbstractHandler
{
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        protected TableUserRepository $tableUserRepository,
        protected TableRepository $tableRepository,
        protected BankService $bankService,
        protected ButtonResolverService $buttonResolverService,
        protected SpectatorRepository $spectatorRepository,
        protected TournamentUserRepository $tournamentUserRepository,
        protected TournamentPrizeRepository $tournamentPrizeRepository,
        protected SuggestPlayerCombinationHandler $suggestPlayerCombinationHandler
    ) {
        parent::__construct($security, $translator);
    }

    protected function getMyPrize(Table $table, ?User $user): array
    {
        if (!$table->getTournament()) {
            return [
                'rank' => 0,
                'sum'  => 0
            ];
        }

        $tournamentUser = $this->tournamentUserRepository->findOneBy([
            'tournament' => $table->getTournament(),
            'user'       => $user
        ]);

        if (!$tournamentUser || !$tournamentUser->getRank()) {
            return [
                'rank' => 0,
                'sum'  => 0
            ];
        }

        $tournamentPrize = $this->tournamentPrizeRepository->findOneBy([
            'tournament' => $table->getTournament(),
            'winner'     => $user
        ]);

        return [
            'rank' => $tournamentUser->getRank(),
            'sum'  => $tournamentPrize?->getSum()
        ];
    }

    public function __invoke(Table $table, ?User $user): array
    {
        $table         = $this->tableRepository->find($table->getId());
        $tableState    = new TableState();
        $currentPlayer = $user ? $this->tableUserRepository->findOneBy(['user' => $user, 'table' => $table]) : null;
        $tableState->setTable($table);
        $players = $this->tableUserRepository->findBy(['table' => $table]);

        if (count($players) > 0) {
            $tableState->setPlayers(...$players);
        }

        $banks = $this->bankService->getTableBanks($table);
        if (!in_array($table->getRound()->value, [Round::FastFinish->value, Round::ShowDown->value], true)) {
            $banks = array_filter($banks, fn($bank) => $bank->getStatus()->value === BankStatus::InProgress->value);
        }

        if (count($banks) > 0) {
            $tableState->setBanks(...$banks);
        }

        $tableState->setCards($table->getCards(), $currentPlayer?->getCards());

        if ($currentPlayer) {
            if ($table->getTurnPlace() === $currentPlayer->getPlace()) {
                $maxBetExcludeCurrentUser = $this->tableUserRepository->getMaxBetExcludeCurrentUser($table, $user);
                $betNavigation            = $this->buttonResolverService->getBetButtons($table, $currentPlayer, $maxBetExcludeCurrentUser);
                $tableState->setBetNavigation($betNavigation);

                $betRange = $this->buttonResolverService->getBetRange($table, $currentPlayer, $maxBetExcludeCurrentUser);
                $tableState->setBetRange($betRange['min'], $betRange['max']);
            }

            $tableState->setMyPlace($currentPlayer->getPlace());
        }

        $tableState->setMyPrize($this->getMyPrize($table, $user));

        $spectators = $this->spectatorRepository->getCountSpectators($table);
        $tableState->setSpectators($spectators);

        $tableState->setMaxBet($this->tableUserRepository->getMaxBet($table));

        $tableState->setSuggestCombination(($this->suggestPlayerCombinationHandler)($table, $user));

        return TableStateResponse::item($tableState, $this->translator, $this->security->getUser());
    }
}
