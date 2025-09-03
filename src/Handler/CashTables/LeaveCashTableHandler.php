<?php

declare(strict_types=1);

namespace App\Handler\CashTables;

use App\Entity\Table;
use App\Entity\User;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Handler\Balance\ReturnRemainingsPlayerBalanceHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Service\PlayerService;
use App\Service\TableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LeaveCashTableHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected PlayerService $playerService,
        protected TableService $tableService,
        protected EntityManagerInterface $entityManager,
        protected ReturnRemainingsPlayerBalanceHandler $returnRemainingsPlayerBalanceHandler,
        protected EventDispatcherInterface $dispatcher,
        protected TableUserRepository $tableUserRepository
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Table $table): array
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (!$table || $table->getTournament()) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TABLE_NOT_FOUND);
        }

        $player = $this->tableUserRepository->findOneBy([
            'user'  => $user->getId(),
            'table' => $table
        ]);

        if (!$player) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::PLAYER_NOT_FOUND);
        }

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->tableService->leaveTable($player);

            if ($table->getTableUsers()->count() < 2) {
                // Обновляем баланс для пользователей за столом.
                ($this->returnRemainingsPlayerBalanceHandler)($player);

                // Удаляем из-за стола всех пользователей которые проиграли или сами пожелали уйти.
                $this->playerService->dropLeavers($table);
                $table->setIsArchived(true);

                $this->entityManager->persist($table);
                $this->entityManager->flush();
            }

            $this->entityManager->getConnection()->commit();
        } catch (ResponseException $e) {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }

        return [];
    }
}
