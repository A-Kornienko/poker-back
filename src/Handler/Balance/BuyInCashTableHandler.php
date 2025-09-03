<?php

declare(strict_types=1);

namespace App\Handler\Balance;

use api\exchange\Currency;
use App\Entity\TableUser;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuyInCashTableHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(TableUser $player, float $chips): void
    {
        $user     = $player->getUser();
        $mainUser = $user->getMainUser();

        if (!$mainUser) {
            return;
        }

        $actualUsdBalance = Currency::converter($mainUser->getBalance(), $mainUser->getUserInfo()['currency'], 'USD');

        if ($actualUsdBalance < $chips) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::INCORRECT_BALANCE);
        }

        $convertedSum = Currency::converter($chips, 'USD', $mainUser->getUserInfo()['currency']);
        $mainUser->changeBalance(-$convertedSum, 'Poker: Connected to table ' . $player->getTable()->getName() . ' with ' . $convertedSum . ' USD');

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $player->setStack($chips);
            $this->entityManager->persist($player);

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->entityManager->getConnection()->rollBack();
            $convertedSum = Currency::converter($chips, 'USD', $mainUser->getUserInfo()['currency']);
            $mainUser->changeBalance($convertedSum, 'Poker: Rollback connection to table ' . $player->getTable()->getName());

            throw $e;
        }
    }
}
