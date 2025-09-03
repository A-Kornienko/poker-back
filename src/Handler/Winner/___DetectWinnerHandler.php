<?php

declare(strict_types=1);

namespace App\Handler\Winner;

use App\Entity\Table;
use App\Entity\TableUser;
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
use App\Repository\WinnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @deprecated
 */
class ___DetectWinnerHandler
{
    public function __construct(
        protected readonly WinnerRepository $winnerRepository,
        protected readonly BankRepository $bankRepository,
        protected readonly TableUserRepository $tableUserRepository,
        protected readonly GetCombinationHandler $getCombinationHandler,
        protected CompareCombinationsHandler $compareCombinationsHandler,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
    ) {
    }

    protected function getTableUserCombinations(array $allActiveTableUsers): array
    {
        $tableUsers = [];
        foreach ($allActiveTableUsers as $tableUser) {
            $cards        = array_merge($tableUser->getCards(), $tableUser->getTable()->getCards());
            $tableUsers[] = $tableUser->setCombination(
                ($this->getCombinationHandler)($tableUser->getTable(), ...$cards)
            );
        }

        return $tableUsers;
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
        /** @var TableUser $tableUser */
        $allActiveTableUsers = $this->tableUserRepository->findBy([
            'table'  => $table,
            'status' => TableUserStatus::Active
        ]);
        // Получаем комбинации карт для юзеров
        $players = match($table->getRound()->value) {
            Round::FastFinish->value => $allActiveTableUsers,
            Round::ShowDown->value   => $this->getTableUserCombinations($allActiveTableUsers),
        };

        if (count($players) < 1) {
            return;
        }

        // Если остался один игрок за столом, отдаем ему все банки сразу
        if (count($players) < 2) {
            reset($banks);
            foreach ($banks as $key => $bank) {
                $bankUserIds = array_map(fn($user) => $user->getId(), $bank->getUsers()->toArray());

                $userId = $players[0]->getUser()->getId();
                if (!in_array($userId, $bankUserIds)) {
                    continue;
                }

                $winner = new Winner();
                $winner->setTable($table)
                    ->setSession($table->getSession())
                    ->setUser($players[0]->getUser())
                    ->setTableUser($players[0])
                    ->setBank($bank)
                    ->setSum($bank->getSum());

                $newStack = Calculator::add($players[0]->getStack(), $bank->getSum());
                $players[0]->setStack($newStack);
                $players[0]->setStatus(TableUserStatus::Winner);
                $bank->setStatus(BankStatus::Completed);

                $this->entityManager->persist($players[0]);
                $this->entityManager->persist($winner);
                $this->entityManager->persist($bank);
                unset($banks[$key]);
            }

            $this->entityManager->flush();

            $winners = $this->winnerRepository->findBy([
                'table'   => $table,
                'session' => $table->getSession(),
            ]);

            $this->dispatcher->dispatch(new WinnerEvent($winners), WinnerEvent::NAME);

            return;
        }

        // Сортировка по рангу и по старшей карте
        usort($tableUsers, function ($a, $b) {
            $combinationA = $a->getCombination();
            $combinationB = $b->getCombination();

            if ($combinationA->getRank() === $combinationB->getRank()) {
                return ($this->compareCombinationsHandler)($combinationA->getCards(), $combinationB->getCards());
            }

            return $combinationB->getRank() <=> $combinationA->getRank();
        });

        for ($i = 0; $i < count($tableUsers); $i++) {
            if (!count($banks)) {
                break;
            }

            $countWinners = 1;
            if (array_key_exists($i + 1, $tableUsers)) {
                if ($tableUsers[$i]->getCombination()->getRank() === $tableUsers[$i + 1]->getCombination()->getRank()) {
                    $countWinners = ($this->compareCombinationsHandler)(
                        $tableUsers[$i + 1]->getCombination()->getCards(),
                        $tableUsers[$i]->getCombination()->getCards(),// Предполагаем что эта комбинация старше
                    );
                }
            }

            // Tут мы понимаем что текущий юзер имеет лучшую комбинацию среди других
            if ($countWinners > 0) {
                reset($banks);
                foreach ($banks as $key => $bank) {
                    $bankUserIds = array_map(fn($user) => $user->getId(), $bank->getUsers()->toArray());

                    $userId = $tableUsers[$i]->getUser()->getId();
                    if (!in_array($userId, $bankUserIds)) {
                        continue;
                    }

                    $winner = new Winner();
                    $winner->setTable($table)
                        ->setSession($table->getSession())
                        ->setUser($tableUsers[$i]->getUser())
                        ->setTableUser($tableUsers[$i])
                        ->setBank($bank)
                        ->setSum($bank->getSum());

                    $chips = Calculator::add($tableUsers[$i]->getStack(), $bank->getSum());
                    $tableUsers[$i]->setStack($chips);
                    $tableUsers[$i]->setStatus(TableUserStatus::Winner);
                    $bank->setStatus(BankStatus::Completed);

                    $this->entityManager->persist($tableUsers[$i]);
                    $this->entityManager->persist($winner);
                    $this->entityManager->persist($bank);
                    unset($banks[$key]);
                }

                continue;
            }

            // Тут мы понимаем что текущий юзер имеет такую же комбинацию что и следуюший
            if ($countWinners < 1) {
                // В таком случае нужно найти всех последующих
                // Сразу запишем в массив тех кто совпал.
                $sameTableUsers = [$tableUsers[$i], $tableUsers[$i + 1]];

                // Пройдем еще раз по всем пользователям и найдем все совпадения с текущей комбинацией.
                for ($j = $i + 2; $j < count($tableUsers); $j++) {
                    $compare = ($this->compareCombinationsHandler)(
                        $tableUsers[$j]->getCombination()->getCards(),
                        $tableUsers[$i]->getCombination()->getCards(),// Предполагаем что эта комбинация старше
                    );

                    if ($compare !== 0) {
                        continue;
                    }

                    $sameTableUsers[] = $tableUsers[$j];
                }

                $sameTableUserIds = array_map(fn($sameTableUser) => $sameTableUser->getUser()->getId(), $sameTableUsers);
                reset($banks);
                //Для получившизся юзеров находим подходящие банки
                foreach ($banks as $key => $bank) {
                    $bankUserIds = array_map(fn($user) => $user->getId(), $bank->getUsers()->toArray());

                    if (count(array_intersect($sameTableUserIds, $bankUserIds)) !== count($sameTableUserIds)) {
                        continue;
                    }

                    foreach ($sameTableUsers as $sameTableUser) {
                        $winner = new Winner();
                        $winSum = Calculator::divide($bank->getSum(), count($sameTableUserIds));
                        $winner->setTable($table)
                            ->setSession($table->getSession())
                            ->setUser($sameTableUser->getUser())
                            ->setTableUser($sameTableUser)
                            ->setBank($bank)
                            ->setSum($winSum);
                        $chips = Calculator::add($sameTableUser->getStack(), $winSum);
                        $sameTableUser->setStack($chips);
                        $sameTableUser->setStatus(TableUserStatus::Winner);
                        $this->entityManager->persist($winner);
                        $this->entityManager->persist($sameTableUser);
                    }

                    $bank->setStatus(BankStatus::Completed);
                    $this->entityManager->persist($bank);
                    unset($banks[$key]);
                }

                continue;
            }
        }

        $this->entityManager->flush();

        $winners = $this->winnerRepository->findBy([
            'table'   => $table,
            'session' => $table->getSession(),
        ]);

        $this->dispatcher->dispatch(new WinnerEvent($winners), WinnerEvent::NAME);
    }
}
