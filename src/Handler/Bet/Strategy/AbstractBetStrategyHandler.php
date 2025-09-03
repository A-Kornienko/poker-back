<?php

declare(strict_types=1);

namespace App\Handler\Bet\Strategy;

use App\Entity\{Table, TableUser, User};
use App\Handler\TableState\RoundHandler;
use App\Handler\TableState\TurnHandler;
use App\Handler\TimeBank\UpdateTimeBankHandler;
use App\Repository\TableUserRepository;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractBetStrategyHandler
{
    protected TableUser $player;

    public function __construct(
        protected Security $security,
        protected TurnHandler $turnHandler,
        protected PlayerService $playerService,
        protected EntityManagerInterface $entityManager,
        protected TableUserRepository $tableUserRepository,
        protected UpdateTimeBankHandler $timeBankHandler,
        protected RoundHandler $roundHandler,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
    ) {
    }

    protected function validateTurn(Table $table, User $user): void
    {
        $this->player = $this->playerService->getTableUser($table, $user);
        $this->turnHandler->validateTurn($table, $this->player);
    }

    protected function updatePlayer(TableUser $player): void
    {
        $player->setBetExpirationTime(0);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    protected function handleTurn(TableUser $currentPlayer)
    {
        $currentPlayer = $this->tableUserRepository->find($currentPlayer->getId());
        $table         = $currentPlayer->getTable();

        $this->timeBankHandler->updateTimeBankAfterTurn($currentPlayer);
        $this->handleLastWord($table, $currentPlayer);

        $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);
        if ($this->roundHandler->isFastFinishRoundStarted($table, $activePlayersSortedByPlace)) {
            return;
        }

        if ($currentPlayer && $table->getLastWordPlace() === $currentPlayer->getPlace()) {
            $table->setRound($table->getRound()->next());
            $this->entityManager->persist($table);
            $this->entityManager->flush();
            $this->roundHandler->startRound($table);

            return;
        }

        $this->turnHandler->changeTurnPlace($table);
    }

    protected function handleLastWord($table, $currentPlayer): void
    {
        $maxBet = $this->tableUserRepository->getMaxBetExcludeCurrentUser($table, $currentPlayer->getUser());
        if ($currentPlayer->getBet() > $maxBet) {
            $this->turnHandler->changeLastWordPlace($table, $currentPlayer);
        }
    }
}
