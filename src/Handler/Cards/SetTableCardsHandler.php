<?php

declare(strict_types=1);

namespace App\Handler\Cards;

use App\Entity\Table;
use App\Enum\CardType;
use Doctrine\ORM\EntityManagerInterface;

class SetTableCardsHandler extends AbstractCardsHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    public function __invoke(Table $table, ?int $countCards = 1): Table
    {
        $excludeCards = $table->getCards();
        foreach ($table->getTableUsers()->toArray() as $tableUser) {
            $excludeCards = array_merge($excludeCards, $tableUser->getCards());
        }

        $previousTableCards = $table->getCards();
        $cards              = $this->getCards($countCards, ...$excludeCards);
        foreach ($cards as &$card) {
            $card->setType(CardType::Table);
        }

        $newTableCards = !$countCards ? $previousTableCards : array_merge($previousTableCards, $cards);
        $table->setCards(...$newTableCards);

        return $table;
    }
}
