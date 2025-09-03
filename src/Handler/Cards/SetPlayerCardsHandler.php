<?php

declare(strict_types=1);

namespace App\Handler\Cards;

use App\Enum\CardType;
use Doctrine\ORM\EntityManagerInterface;

class SetPlayerCardsHandler extends AbstractCardsHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    public function __invoke(array $activePlayersSortedByPlace, ?int $countCards = 2): void
    {
        $excludedCards = [];
        /** @var TableUser $tableUser */
        foreach ($activePlayersSortedByPlace as $tableUser) {
            $userCards = $this->getCards($countCards, ...$excludedCards);

            foreach ($userCards as &$card) {
                $card->setType(CardType::Hand);
            }

            $tableUser->setCards(...$userCards);
            $excludedCards = array_merge($excludedCards, $userCards);
            $this->entityManager->persist($tableUser);
            $this->entityManager->flush();
        }
    }
}
