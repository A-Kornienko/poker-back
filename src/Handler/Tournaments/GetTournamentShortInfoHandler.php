<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Response\TournamentResponse;
use App\Service\TableService;
use App\Service\TournamentService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentShortInfoHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableService $tableService,
        protected TournamentService $tournamentService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament = null): array
    {
        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        return TournamentResponse::shortInfo($tournament, $this->translator, $this->security->getUser());
    }
}
