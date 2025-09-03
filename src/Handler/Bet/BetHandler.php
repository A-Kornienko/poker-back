<?php

declare(strict_types=1);

namespace App\Handler\Bet;

use App\Entity\Table;
use App\Enum\BetType;
use App\Handler\AbstractHandler;
use App\Handler\TableState\GetTableStateHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BetHandler extends AbstractHandler
{
    protected iterable $betHandlers;

    public function __construct(
        #[TaggedIterator('app.betHandlers')]
        iterable $betHandlers,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $dispatcher,
        protected GetTableStateHandler $getTableStateHandler,
    ) {
        parent::__construct($security, $translator);

        $this->betHandlers = $betHandlers;
    }

    public function __invoke(Table $table, BetType $betType, Request $request): void
    {
        $user   = $this->security->getUser();
        $amount = (float) $this->getJsonParam($request, 'amount');

        foreach ($this->betHandlers as $betHandler) {
            if ($betHandler->isApplicable($betType)) {
                $betHandler($table, $user, $amount);

                break;
            }
        }
    }
}
