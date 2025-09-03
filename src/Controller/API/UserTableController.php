<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\UserRole;
use App\Exception\ResponseException;
use App\Handler\TimeBank\ActivateTimeBankHandler;
use App\Response\TableResponse;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\{JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserTableController extends BaseApiController
{
    #[Route('/api/my_tables', name: 'get_user_tables', methods: 'GET')]
    #[IsGranted(UserRole::Player->value)]
    public function getMyTables(
        UserService $userService
    ): JsonResponse {
        $response = [];

        try {
            $user     = $this->security->getUser();
            $tables   = $userService->getMyTables($user);
            $response = TableResponse::collection(...$tables);
        } catch (ResponseException $exception) {
            return $this->response($response, $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route('/api/table/{table}/activate_time_bank', name: 'activate_time_bank', methods: 'POST')]
    #[IsGranted(UserRole::Player->value)]
    public function activateTimeBank(
        Table $table,
        ActivateTimeBankHandler $activateTimeBankHandler
    ): JsonResponse {
        $user     = $this->security->getUser();
        $response = [];

        try {
            $response = $activateTimeBankHandler($table, $user);
        } catch (ResponseException $exception) {
            return $this->response($response, $exception->getCode());
        }

        return $this->response($response);
    }
}
