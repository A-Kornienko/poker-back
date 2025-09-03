<?php

declare(strict_types=1);

namespace App\Handler\Cards\Combination;

use AllowDynamicProperties;
use App\Entity\Table;
use App\Enum\CardCombinationRank;
use App\Enum\Rules;
use App\Handler\Cards\Combination\OmahaHigh\CombinationHandlerInterface as OmahaHighCombinationHandlerInterface;
use App\Handler\Cards\Combination\TexasHoldem\CombinationHandlerInterface as TexasHoldemCombinationHandlerInterface;
use App\ValueObject\Card;
use App\ValueObject\Combination;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AllowDynamicProperties]
class GetCombinationHandler
{
    protected iterable $combinationTexasHoldemHandlers;

    protected iterable $combinationOmahaHighHandlers;

    public function __construct(
        #[TaggedIterator('app.combinationTexasHoldemHandlers')]
        iterable $combinationTexasHoldemHandlers,
        #[TaggedIterator('app.combinationOmahaHighHandlers')]
        iterable $combinationOmahaHighHandlers,
    ) {
        $this->combinationTexasHoldemHandlers = $combinationTexasHoldemHandlers;
        $this->combinationOmahaHighHandlers   = $combinationOmahaHighHandlers;
    }

    private function getOmahaHighCombination(Card ...$cards): Combination
    {
        /** @var OmahaHighCombinationHandlerInterface $combinationOmahaHighHandler */
        foreach ($this->combinationOmahaHighHandlers as $combinationOmahaHighHandler) {
            $combination = $combinationOmahaHighHandler->getCombination(...$cards);
            if ($combination) {
                return $combination;
            }
        }

        return (new Combination())
            ->setName(CardCombinationRank::HighCard->name)
            ->setRank(CardCombinationRank::HighCard->value)
            ->setCards(array_slice($cards, 0, 5));
    }

    private function getTexasHoldemCombination(Card ...$cards): Combination
    {
        /** @var TexasHoldemCombinationHandlerInterface $combinationTexasHoldemHandler */
        foreach ($this->combinationTexasHoldemHandlers as $combinationTexasHoldemHandler) {
            $combination = $combinationTexasHoldemHandler->getCombination(...$cards);
            if ($combination) {
                return $combination;
            }
        }

        usort($cards, fn($prev, $next) => $next->getValue() <=> $prev->getValue());

        return (new Combination())
            ->setName(CardCombinationRank::HighCard->name)
            ->setRank(CardCombinationRank::HighCard->value)
            ->setCards(array_slice($cards, 0, 5));
    }

    public function __invoke(Table $table, Card ...$cards): Combination
    {
        return match ($table->getSetting()->getRule()) {
            Rules::TexasHoldem => $this->getTexasHoldemCombination(...$cards),
            Rules::OmahaHigh   => $this->getOmahaHighCombination(...$cards),
            default            => throw new \Exception('Unknown table rule'),
        };
    }
}
