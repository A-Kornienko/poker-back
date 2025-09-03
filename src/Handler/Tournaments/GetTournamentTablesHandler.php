<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableRepository;
use App\Response\TableResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentTablesHandler extends AbstractHandler
{
    public function __construct(
        protected TableRepository $tableRepository,
        protected Security $security,
        protected TranslatorInterface $translator
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Request $request, ?Tournament $tournament = null): array
    {
        $page  = $request->query->getInt(static::REQUEST_PAGE, 1);
        $limit = $request->query->getInt(static::REQUEST_LIMIT, 20);

        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        $tables = $this->tableRepository->getTournamentCollection($tournament, $page, $limit);

        return $tables ? TableResponse::collection(...$tables) : [];
    }
}
