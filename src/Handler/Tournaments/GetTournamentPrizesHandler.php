<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use App\Entity\Tournament;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetTournamentPrizesHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator
    ) {
        parent::__construct($security, $translator);
    }

    protected function getCollection(Tournament $tournament): array
    {
        $tournamentPrizes = $tournament->getPrizes()->toArray();

        usort($tournamentPrizes, fn($a, $b) => $b->getSum() <=> $a->getSum());

        $prizes = [];
        foreach ($tournamentPrizes as $index => $tournamentPrize) {
            $prizes[] = [
                'position' => $index + 1,
                'winner'   => (bool) $tournamentPrize->getWinner(),
                'sum'      => $tournamentPrize->getSum(),
            ];
        }

        return $prizes;
    }

    public function __invoke(?Tournament $tournament = null): array
    {
        if (!$tournament) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_TOURNAMENT);
        }

        $response              = $this->getCollection($tournament);
        $response['total']     = count($response);
        $response['prizeRule'] = $tournament->getSetting()->getPrizeRule();

        return $response ?: [];
    }
}
