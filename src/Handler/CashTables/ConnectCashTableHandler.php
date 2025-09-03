<?php

declare(strict_types=1);

namespace App\Handler\CashTables;

use App\Entity\Table;
use App\Entity\{TableSetting, TableUser};
use App\Enum\{TableUserStatus, TableState};
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Handler\Balance\BuyInCashTableHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Service\PlayerService;
use App\Service\TableService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConnectCashTableHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableService $tableService,
        protected PlayerService $playerService,
        protected BuyInCashTableHandler $buyInCashTableHandler,
        protected EntityManagerInterface $entityManager,
        protected TableUserRepository $tableUserRepository
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Request $request, ?TableSetting $tableSetting): int
    {
        if (!$tableSetting) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_SETTINGS);
        }

        $user   = $this->security->getUser();
        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user->getId(),
            'table' => $tableSetting->getTables()->toArray()
        ]);

        if ($player) {
            return $player->getTable()->getId();
        }

        $stack = (float) $this->getJsonParam($request, 'stack');
        if ($stack < $tableSetting->getBuyIn()) {
            $stack = $tableSetting->getBuyIn();
        }

        $tables = $this->tableService->getEmptyPlaceTablesBySetting($tableSetting);
        /** @var Table $table */
        $table  = count($tables) > 0 ? current($tables) : $this->tableService->create($tableSetting);

        $optimalPlace = $this->getOptimalPlace($table);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $table->setIsArchived(false);
            $player = $this->playerService->create($table, $user, $optimalPlace, $stack);

            $this->setPlayerStatus($table, $player);

            ($this->buyInCashTableHandler)($player, $stack);

            $this->entityManager->persist($table);
            $this->entityManager->flush();

            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }

        return $player->getTable()->getId();
    }

    protected function getOptimalPlace(Table $table): int
    {
        $places = $table->getFreePlaces();
        $bigBlindPlace = $table->getBigBlindPlace();

        foreach ($places as $place) {
            if ($bigBlindPlace < $place) {
                return $place;
            }
        }

        return $places[0];
    }

    protected function setPlayerStatus(Table $table, TableUser $player): void
    {
        $playersCount = $table->getTableUsers()->count();
        $bigBlindPlace = $table->getBigBlindPlace();

        if ($playersCount > 2) {
            $player->setStatus(TableUserStatus::WaitingBB);
        }

        if ($bigBlindPlace === $player->getPlace() - 1
            || ($bigBlindPlace === 10 && $player->getPlace() === 1)
        ) {
            $player->setStatus(TableUserStatus::Pending);
        }
    }
}
