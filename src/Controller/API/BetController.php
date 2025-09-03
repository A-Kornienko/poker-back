<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\BetType;
use App\Enum\UserRole;
use App\Exception\ResponseException;
use App\Handler\Bet\{BetHandler, SetAutoBlindStatusHandler};
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BetController extends BaseApiController
{
    #[Route(path: '/api/bet/{table}/{betType}', name: 'bet', methods: ['POST'])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function bet(
        Table $table,
        BetType $betType,
        Request $request,
        BetHandler $betHandler
    ): JsonResponse {
        try {
            $betHandler($table, $betType, $request);
        } catch (ResponseException $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }

    #[Route(path: '/api/auto-blind/{table}', name: 'auto_blind', methods: ['POST'])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function autoBlind(
        Table $table,
        SetAutoBlindStatusHandler $setAutoBlindStatusHandler
    ): JsonResponse {
        try {
            $setAutoBlindStatusHandler($table);
        } catch (ResponseException $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([]);
    }
}
