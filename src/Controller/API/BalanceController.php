<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\UserRole;
use App\Exception\ResponseException;
use App\Handler\Balance\{Rebuy\ReBuyAfterLoseHandler,
    Rebuy\ReBuyAfterTournamentLoseHandler,
    Rebuy\ReBuyCashTableHandler,
    Rebuy\ReBuyTournamentHandler};
use App\Security\Voter\TableVoter;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BalanceController extends BaseApiController
{
    #[Route(path: '/api/{table}/rebuy', name: 'rebuy', methods: ['POST'])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function rebuy(
        Table $table,
        Request $request,
        ReBuyCashTableHandler $reBuyCashTableHandler,
        ReBuyTournamentHandler $reBuyTournamentHandler,
    ): JsonResponse {
        $user  = $this->security->getUser();
        $chips = (float) ($this->getJsonParam($request, 'chips'));

        try {
            $table->getTournament()
                ? $reBuyTournamentHandler($user, $table)
                : $reBuyCashTableHandler($user, $table, $chips);
        } catch (ResponseException $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }

    #[Route(path: '/api/{table}/rebuy-after-lose', name: 'rebuy_after_lose', methods: ['POST'])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    #[IsGranted(TableVoter::IS_LOSER, 'table')]
    public function rebuyAfterLose(
        Table $table,
        Request $request,
        ReBuyAfterLoseHandler $reBuyAfterLoseHandler,
        ReBuyAfterTournamentLoseHandler $reBuyAfterTournamentLoseHandler
    ): JsonResponse {
        $user  = $this->security->getUser();
        $chips = (float) ($this->getJsonParam($request, 'chips'));

        try {
            $table->getTournament()
                ? $reBuyAfterTournamentLoseHandler($user, $table)
                : $reBuyAfterLoseHandler($user, $table, $chips);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }
}
