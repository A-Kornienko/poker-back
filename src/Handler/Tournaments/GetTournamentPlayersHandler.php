<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Repository\TournamentRepository;
use App\Response\PlayerResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentPlayersHandler extends AbstractHandler
{
    public function __construct(
        protected TableUserRepository $tableUserRepository,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TournamentRepository $tournamentRepository,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?Tournament $tournament = null): array
    {
        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        $players = $tournament->getTournamentUsers()->toArray();

        return $players ? PlayerResponse::tournamentCollection($tournament, $players) : [];
    }
}
