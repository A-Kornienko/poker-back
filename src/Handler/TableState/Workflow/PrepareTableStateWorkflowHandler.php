<?php

namespace App\Handler\TableState\Workflow;

use App\Entity\Table;
use App\Entity\TableUser;
use App\Enum\TableUserStatus;
use App\Service\PlayerService;
use App\Service\TableService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class PrepareTableStateWorkflowHandler implements TableStateWorkflowHandlerInterface
{
    protected const NAME = 'prepareTable';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TableService $tableService,
        protected PlayerService $playerService
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Обновляем данные стола и игроков.
     *
     * @param TransitionEvent $event
     * @throws Exception
     * @return void
     */
    public function runTransition(TransitionEvent $event): void
    {
        /** @var Table $table $table */
        $table = $event->getSubject();

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->tableService->refreshTable($table); // Обновляем стол.
            $this->playerService->refreshPlayers($table); // Обновляем игроков.

            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw new Exception($e->getMessage());
        }
    }

    /**
     * Проверяет, что стол и игроки обновлены
     *
     * @param EnterEvent $event
     * @throws Exception
     * @return void
     */
    public function runEnter(EnterEvent $event): void
    {
        /** @var Table $table */
        $table = $event->getSubject();

        $countActivePlayers = $table->getTableUsers()->filter(
            fn(TableUser $player) => in_array($player->getStatus()->value, [
                TableUserStatus::Active->value, 
                TableUserStatus::WaitingBB->value, 
                TableUserStatus::AutoBlind->value
            ])
        )->count();

        if ($countActivePlayers < 2) {
            throw new \Exception('Enter in PrepareTableStateWorkflowHandler has blocked because count players less the 2', 2000);
        }

        if (!$this->isTableRefreshed($table) || !$this->isPlayersRefreshed($table)) {
            throw new \Exception('Enter in PrepareTableStateWorkflowHandler has blocked because players or table have not been refreshed', 2000);
        }
    }

    protected function isPlayersRefreshed(Table $table): bool
    {
        $tableUsers = $table->getTableUsers()->toArray();

        foreach ($tableUsers as $player) {
            $isRefreshed = match (true) {
                (bool) $player->getBet()                         => false,
                (bool) $player->getBetSum()                      => false,
                $player->getBetType() !== null                   => false,
                $player->getBetExpirationTime() !== 0            => false,
                !empty($player->getCards())                      => false,
                default                                          => true,
            };

            if (!$isRefreshed) {
                return $isRefreshed;
            }
        }

        return true;
    }

    protected function isTableRefreshed(Table $table): bool
    {
        return match (true) {
            (bool) $table->getTurnPlace()     => false,
            (bool) $table->getLastWordPlace() => false,
            !empty($table->getCards())        => false,
            $table->getRakeStatus()           => false,
            default                           => true,
        };
    }
}
