<?php

namespace App\Handler\TableHistory\Add;

use App\Entity\Bank;
use App\Enum\Round;
use App\Event\TableHistory\PotEvent;
use App\Helper\Calculator;
use App\Repository\TableHistoryRepository;
use App\ValueObject\TableHistory\PotTableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PotTableHistoryHandler implements AddTableHistoryHandlerInterface
{
    public function __construct(
        protected TableHistoryRepository $tableHistoryRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getRelatedEvent(): string
    {
        return PotEvent::NAME;
    }

    public function __invoke(Event $event): void
    {
        /** @var PotEvent $event */
        $table = $event->getTable();
        $banks = $event->getBanks();

        $tableHistory = $this->tableHistoryRepository->findOneBy([
            'table'   => $table,
            'session' => $table->getSession()
        ]);

        $potTableHistory = $tableHistory->getPot() ?? new PotTableHistory();
        $banksSum        = array_map(fn(Bank $bank) => $bank->getSum(), $banks);
        $pot             = array_reduce($banksSum, fn($carry, $item) => Calculator::add($carry, $item), 0);

        match($table->getRound()->value) {
            Round::PreFlop->value => $potTableHistory->setPreFlop($pot),
            Round::Flop->value    => $potTableHistory->setFlop($pot),
            Round::Turn->value    => $potTableHistory->setTurn($pot),
            Round::River->value   => $potTableHistory->setRiver($pot),
            default               => true,
        };

        $tableHistory->setPot($potTableHistory);

        $this->entityManager->persist($tableHistory);
        $this->entityManager->flush();
    }
}
