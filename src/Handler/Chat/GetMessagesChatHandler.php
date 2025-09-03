<?php

declare(strict_types=1);

namespace App\Handler\Chat;

use App\Entity\Table;
use App\Handler\AbstractHandler;
use App\Repository\ChatRepository;
use App\Response\ChatResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetMessagesChatHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected ChatRepository $chatRepository
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Table $table, Request $request)
    {
        $page  = $request->query->getInt(static::REQUEST_PAGE, 1);
        $limit = $request->query->getInt(static::REQUEST_LIMIT, 20);

        if (!$page) {
            $countMessages = $this->chatRepository->count(['table' => $table]);
            $page          = (int) ceil($countMessages / $limit);
        }

        $chatCollection = $this->chatRepository->getCollection($table, $page, $limit);

        return [
            'items'      => $chatCollection['items'] ? ChatResponse::collection($chatCollection['items'], $this->security->getUser()) : [],
            'pagination' => [
                'total' => $chatCollection['total'] ?? 0,
                'page'  => $page,
                'limit' => $limit,
                'pages' => $chatCollection['total'] ? ceil($chatCollection['total'] / $limit) : $page
            ],
        ];
    }
}
