<?php

declare(strict_types=1);

namespace App\Handler\TableHistory\Add;

use App\Entity\TableHistory;
use App\Enum\Round;
use App\Event\TableHistory\StartGameEvent;
use App\Helper\Calculator;
use App\ValueObject\TableHistory\BlindsTableHistory;
use App\ValueObject\TableHistory\PlayerTableHistory;
use App\ValueObject\TableHistory\PotTableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class StartGameTableHistoryHandler implements AddTableHistoryHandlerInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getRelatedEvent(): string
    {
        return StartGameEvent::NAME;
    }

    public function __invoke(Event $event): void
    {
        /** @var StartGameEvent $event */
        $table = $event->getTable();

        foreach ($table->getTableUsers() as $player) {
            $players[] = (new PlayerTableHistory())->fromArray([
                'place' => $player->getPlace(),
                'login' => $player->getUser()->getLogin(),
                'stack' => $player->getStack(),
            ]);
        }

        $tableHistory = (new TableHistory())
            ->setTable($table)
            ->setSession($table->getSession())
            ->setDealer($table->getDealerPlace())
            ->setBlinds((new BlindsTableHistory())->fromArray([
                'smallBlindPlace' => $table->getSmallBlindPlace(),
                'bigBlindPlace' => $table->getBigBlindPlace(),
                'smallBlind'    => $table->getSmallBlind(),
                'bigBlind'      => $table->getBigBlind(),
            ]))
            ->setPlayers($players)
            ->setPot((new PotTableHistory())->fromArray([
                Round::PreFlop->value => Calculator::add(
                    $table->getSmallBlind(), 
                    $table->getBigBlind()
                ),
            ]));

        $this->entityManager->persist($tableHistory);
        $this->entityManager->flush();
    }
}
