<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\UserRole;
use App\Exception\ResponseException;
use App\Handler\Afk\BackToGameHandler;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TableController extends BaseApiController
{
    #[Route('/api/tables/{table}/back-to-game', name: 'back_to_game', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function backToGame(Table $table, BackToGameHandler $backToGameHandler): JsonResponse
    {
        $user = $this->security->getUser();

        try {
            $backToGameHandler($table, $user);
        } catch (ResponseException $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }
}
