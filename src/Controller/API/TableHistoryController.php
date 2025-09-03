<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\UserRole;
use App\Handler\TableHistory\GetTableHistoryHandler;
use App\Handler\TableHistory\GetTableHistoryListHandler;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/table-history')]
class TableHistoryController extends BaseApiController
{
    #[Route(path: '/{table}', name: 'table_history_list', methods: [Request::METHOD_GET])]
    #[IsGranted(UserRole::Player->value)]
    public function table(
        Table                      $table,
        Request                    $request,
        GetTableHistoryListHandler $getTableHistoryListHandler,
    ): JsonResponse {
        try {
            return $this->response(
                $getTableHistoryListHandler($table, $request)
            );
        } catch (Exception $exception) {
            return $this->response([], $exception->getCode());
        }
    }

    #[Route(path: '/details/{session}', name: 'table_history_details', methods: [Request::METHOD_GET])]
    #[IsGranted(UserRole::Player->value)]
    public function session(
        string              $session,
        GetTableHistoryHandler $getTableHistoryHandler
    ): JsonResponse {
        try {
            return $this->response(
                $getTableHistoryHandler($session, $this->security->getUser())
            );
        } catch (Exception $exception) {
            return $this->response([], $exception->getCode());
        }
    }
}
