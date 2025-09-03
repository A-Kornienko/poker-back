<?php

declare(strict_types=1);

namespace App\Handler\Bank;

use App\Entity\Bank;
use App\Entity\Table;
use App\Enum\BankStatus;
use App\Enum\BetType;
use App\Event\TableHistory\PotEvent;
use App\Handler\AbstractHandler;
use App\Helper\Calculator;
use App\Repository\BankRepository;
use App\Repository\TableUserRepository;
use App\Service\BankService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CalculateBankHandler extends AbstractHandler
{
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        protected readonly BankRepository $bankRepository,
        protected readonly TableUserRepository $tableUserRepository,
        protected readonly EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
        protected BankService $bankService
    ) {
        parent::__construct($security, $translator);
    }

    protected function calculateNewBanks(Table $table): void
    {
        $activeTableUsers = $this->tableUserRepository->getPlayersSortedByBetSumAsc($table);

        $betSumCache  = [];
        $newBanks     = [];
        $totalBankSum = 0;
        $bankCount    = 0;

        while(count($activeTableUsers) > 0) {
            $bankSum    = 0;
            $bankBet    = 0;
            $bankUsers  = [];
            $removeKeys = [];
            reset($activeTableUsers);

            foreach ($activeTableUsers as $key => $activeTableUser) {
                if (!array_key_exists($activeTableUser->getId(), $betSumCache)) {
                    $betSumCache[$activeTableUser->getId()] = $activeTableUser->getBetSum();
                }

                if ($betSumCache[$activeTableUser->getId()] <= 0) {
                    $removeKeys[] = $key;
                    continue;
                }

                if ($activeTableUser->getBetType()?->value === BetType::Fold->value) {
                    if ($betSumCache[$activeTableUser->getId()] <= 0) {
                        $betSumCache[$activeTableUser->getId()] = $activeTableUser->getBet();
                    }

                    if ($betSumCache[$activeTableUser->getId()] <= 0) {
                        $removeKeys[] = $key;
                        continue;
                    }

                    if ($bankBet <= 0) {
                        $bankSum                                = Calculator::add($bankSum, $betSumCache[$activeTableUser->getId()]);
                        $betSumCache[$activeTableUser->getId()] = 0;
                        $removeKeys[]                           = $key;
                        continue;
                    }

                    $betSum                                 = Calculator::subtract($betSumCache[$activeTableUser->getId()], $bankBet);
                    $betSumCache[$activeTableUser->getId()] = $betSum;
                    $bankSum                                = Calculator::add($bankSum, $bankBet);

                    continue;
                }

                if ($bankBet <= 0) {
                    $bankBet                                = $betSumCache[$activeTableUser->getId()];
                    $bankSum                                = Calculator::add($bankSum, $betSumCache[$activeTableUser->getId()]);
                    $bankUsers[]                            = $activeTableUser->getUser();
                    $betSumCache[$activeTableUser->getId()] = 0;
                    $removeKeys[]                           = $key;
                    continue;
                }

                $betSum                                 = Calculator::subtract($betSumCache[$activeTableUser->getId()], $bankBet);
                $betSumCache[$activeTableUser->getId()] = $betSum;
                $bankSum                                = Calculator::add($bankSum, $bankBet);
                $bankUsers[]                            = $activeTableUser->getUser();

                if ((int) $betSum <= 0) {
                    $removeKeys[] = $key;
                }
            }

            foreach ($removeKeys as $keyToRemove) {
                unset($activeTableUsers[$keyToRemove]);
            }

            if (count($bankUsers) < 1) {
                continue;
            }

            $bank = (new Bank())
                ->setBet($bankBet)
                ->setSum($bankSum)
                ->setTable($table)
                ->setStatus(BankStatus::InProgress)
                ->setSession($table->getSession());

            foreach ($bankUsers as $user) {
                $bank->addUser($user);
            }

            $this->entityManager->persist($bank);

            $newBanks[] = $bank;
            $totalBankSum += $bankSum;
            $bankCount++;
        }

        if ($bankCount > 0 && $table->getRakeStatus() && !$table->getTournament()) {
            $totalRake = min(Calculator::multiply($totalBankSum, $table->getSetting()->getRake()), $table->getSetting()->getRakeCap());

            foreach ($newBanks as $bank) {
                $bankPercantage = Calculator::divide($bank->getSum(), $totalBankSum);
                $bankRake       = Calculator::multiply($bankPercantage, $totalRake);

                $bank->setRake($bankRake);
                $bank->setSum(Calculator::subtract($bank->getSum(), $bankRake));
                $this->entityManager->persist($bank);
            }
        }

        $this->entityManager->flush();

        $banks = $this->bankRepository->findBy([
            'table'   => $table,
            'session' => $table->getSession(),
        ]);

        $this->dispatcher->dispatch(new PotEvent($table, $banks), PotEvent::NAME);
    }

    public function __invoke(Table $table): void
    {
        $banks = $this->bankService->getTableBanks($table);

        foreach ($banks as $bank) {
            $this->entityManager->remove($bank);
        }
        $this->entityManager->flush();

        $this->calculateNewBanks($table);
        $this->entityManager->flush();
    }
}
