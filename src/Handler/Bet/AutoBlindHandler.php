<?php

declare(strict_types=1);

namespace App\Handler\Bet;

use App\Entity\Table;
use App\Enum\{BetType, TableUserStatus};
use App\Handler\AbstractHandler;
use App\Helper\Calculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class AutoBlindHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Table $table): void
    {
        foreach ($table->getTableUsers() as $player) {
            if ($player->getStatus() === TableUserStatus::AutoBlind) {
                $bigBlindAmount = $table->getBigBlind();

                $player->setBet($bigBlindAmount);
                $player->setBetType(BetType::Call);
                $player->setStack(Calculator::subtract($player->getStack(), $bigBlindAmount));
                $player->setStatus(TableUserStatus::Active);

                $this->entityManager->persist($player);
            }
        }

        $this->entityManager->flush();
    }
}
