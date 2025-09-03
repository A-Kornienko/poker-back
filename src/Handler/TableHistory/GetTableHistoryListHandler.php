<?php

declare(strict_types=1);

namespace App\Handler\TableHistory;

use App\Entity\Table;
use App\Handler\AbstractHandler;
use App\Helper\Calculator;
use App\Repository\TableHistoryRepository;
use App\ValueObject\Card;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTableHistoryListHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableHistoryRepository $tableHistoryRepository
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Table $table, Request $request): array
    {
        $page  = $request->query->getInt(static::REQUEST_PAGE, 1);
        $limit = $request->query->getInt(static::REQUEST_LIMIT, 20);

        $tableHistoryCollection = $this->tableHistoryRepository->getCollection($table, $page, $limit);
        $items                  = [];

        /** @var TableHistory $message */
        foreach ($tableHistoryCollection['items'] as $tableHistory) {
            $bank    = 0;
            $winners = [];

            foreach ($tableHistory->getWinners() as $winner) {
                if ($winner->getLogin() === $this->security->getUser()?->getLogin()) {
                    $items[$tableHistory->getSession()]['cards'] = array_map(fn(Card $card) => $card->toArray(), $winner->getHandCards()) ?? [];
                }

                $winners[] = $winner->getLogin();
                $bank      = Calculator::add($bank, $winner->getSum());
            }

            $items[$tableHistory->getSession()]['winners'] = $winners;
            $items[$tableHistory->getSession()]['bank']    = $bank;
        }

        return [
            'items'      => $items ?? [],
            'pagination' => [
                'total' => $tableHistoryCollection['total'],
                'page'  => $page,
                'limit' => $limit,
                'pages' => $tableHistoryCollection['total'] ? ceil($tableHistoryCollection['total'] / $limit) : 0,
            ],
        ];
    }
}
