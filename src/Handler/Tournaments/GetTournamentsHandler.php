<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Handler\AbstractHandler;
use App\Repository\TournamentRepository;
use App\Response\TournamentResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentsHandler extends AbstractHandler
{
    public function __construct(
        protected TournamentRepository $tournamentRepository,
        protected Security $security,
        protected TranslatorInterface $translator,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Request $request): array
    {
        $page  = $request->query->getInt(static::REQUEST_PAGE, 1);
        $limit = $request->query->getInt(static::REQUEST_LIMIT, 20);
        $rule  = $request->query->getString(static::REQUEST_RULE);
        $type  = $request->query->getString(static::REQUEST_TYPE);

        $buyInRequest = $request->get('buyIn', []);

        $buyIn = [
            'min' => isset($buyInRequest['min']) ? (int)$buyInRequest['min'] : null,
            'max' => isset($buyInRequest['max']) ? (int)$buyInRequest['max'] : null
        ];

        $tournamentCollection = $this->tournamentRepository->getCollection($buyIn, $page, $limit, $rule, $type);

        return [
            'items'      => $tournamentCollection['items'] ? TournamentResponse::collection($this->translator, ...$tournamentCollection['items']) : [],
            'pagination' => [
                'total' => $tournamentCollection['total'],
                'page'  => $page,
                'limit' => $limit,
                'pages' => $tournamentCollection['items'] ? ceil($tournamentCollection['total'] / $limit) : $page
            ],
        ];
    }
}
