<?php

declare(strict_types=1);

namespace App\Handler\Winner;

use App\Entity\Table;
use App\Entity\Winner;
use App\Enum\BankStatus;
use App\Enum\Round;
use App\Enum\TableUserStatus;
use App\Event\TableHistory\WinnerEvent;
use App\Handler\Cards\Combination\CompareCombinationsHandler;
use App\Handler\Cards\Combination\GetCombinationHandler;
use App\Helper\Calculator;
use App\Repository\BankRepository;
use App\Repository\TableUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DetectWinnerHandler 
{
    public function __construct(
        protected readonly BankRepository $bankRepository,
        protected readonly GetCombinationHandler $getCombinationHandler,
        protected CompareCombinationsHandler $compareCombinationsHandler,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
        protected readonly TableUserRepository $playerRepository,
    ) {
    }

    protected function getPlayerCombinations(array $allActivePlayers): array
    {
        $players = [];
        foreach ($allActivePlayers as $player) {
            $cards        = array_merge($player->getCards(), $player->getTable()->getCards());
            $players[] = $player->setCombination(
                ($this->getCombinationHandler)($player->getTable(), ...$cards)
            );
        }

        return $players;
    }

    public function __invoke(Table $table): void
    {
        $banks = $this->bankRepository->findBy([
            'table'   => $table,
            'status'  => BankStatus::InProgress,
            'session' => $table->getSession()
        ]);

        // Сортировка банков по количеству юзеров
        usort($banks, fn($a, $b) => $a->getUsers()->count() <=> $b->getUsers()->count());

        // Получить юзеров от Table
        /** @var player $player */
        $allActivePlayers = $this->playerRepository->findBy([
            'table'  => $table,
            'status' => TableUserStatus::Active
        ]);
        // Получаем комбинации карт для юзеров
        $players = match($table->getRound()->value) {
            Round::FastFinish->value => $allActivePlayers,
            Round::ShowDown->value   => $this->getPlayerCombinations($allActivePlayers),
        };

        if (count($players) < 1) {
            return;
        }

        $this->resolveWinners($banks, $players);

        return;
    }

    protected function resolveWinners(array $banks, array $players): void
    {
        // Группируем игроков по ид пользователя для удобства доступа к конкретным игрокам
        $indexedPlayersByUserId = [];
        foreach ($players as $player) {
            $indexedPlayersByUserId[$player->getUser()->getId()] = $player;
        }

        $winners = [];
        foreach ($banks as $bank) {
            $bankPlayers = [];
            foreach ($bank->getUsers() as $user) {
                if (!array_key_exists($user->getId(), $indexedPlayersByUserId)) {
                    continue;
                }

                $bankPlayers[] = $indexedPlayersByUserId[$user->getId()];
            }

            if ($bankPlayers > 1) {
                // Сортировка игроков по комбинации.
                usort($bankPlayers, function ($playerA, $playerB) {
                    $combinationA = $playerA->getCombination();
                    $combinationB = $playerB->getCombination();

                    if ($combinationA->getRank() === $combinationB->getRank()) {
                        return ($this->compareCombinationsHandler)($combinationA->getCards(), $combinationB->getCards());
                    }

                    return $combinationB->getRank() <=> $combinationA->getRank();
                });
            }

            $bankWinners = [];
            for ($i = 0; $i < count($bankPlayers); $i++) {
                if (array_key_exists($i + 1, $bankPlayers)) {
                    if ($bankPlayers[$i]->getCombination()->getRank() !== $bankPlayers[$i + 1]->getCombination()->getRank()) {
                        $bankWinners[$bankPlayers[$i]->getId()] = $bankPlayers[$i];
    
                        break;
                    }

                    $comparedCombination = ($this->compareCombinationsHandler)(
                        $bankPlayers[$i + 1]->getCombination()->getCards(),
                        $bankPlayers[$i]->getCombination()->getCards(),// Предполагаем что эта комбинация старше
                    );

                    if ($comparedCombination === 0) {
                        $bankWinners[$bankPlayers[$i]->getId()] = $bankPlayers[$i + 1];
                    }

                    continue;
                }

                if (count($bankWinners) < 1) {
                    $bankWinners[$bankPlayers[$i]->getId()] = $bankPlayers[$i];
                }
            }

            $winSum = Calculator::subtract($bank->getSum(), count($bankWinners));
            foreach ($bankWinners as $bankWinner) {
                $winners[] = (new Winner())->setTable($bank->getTable())
                    ->setSession($bank->getTable()->getSession())
                    ->setUser($bankWinner->getUser())
                    ->setTableUser($bankWinner)
                    ->setBank($bank)
                    ->setSum($winSum);
            }

            $bank->setStatus(BankStatus::Completed);

            $this->entityManager->persist($bank);
        }

        foreach ($winners as $winner) {
            $playerStack = $indexedPlayersByUserId[$winner->getUser()->getId()]->getStack();
            $newStack = Calculator::add($playerStack, $winner->getSum());
            $indexedPlayersByUserId[$winner->getUser()->getId()]->setStack($newStack);
            $indexedPlayersByUserId[$winner->getUser()->getId()]->setStatus(TableUserStatus::Winner);
            $this->entityManager->persist($winner);
            $this->entityManager->persist($indexedPlayersByUserId[$winner->getUser()->getId()]);
        }

        $this->entityManager->flush();

        $this->dispatcher->dispatch(new WinnerEvent($winners), WinnerEvent::NAME);
    }
}
