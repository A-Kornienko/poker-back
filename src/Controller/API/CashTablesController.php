<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Entity\TableSetting;
use App\Enum\UserRole;
use App\Handler\CashTables\ConnectCashTableHandler;
use App\Handler\CashTables\GetCashTableDetailsHandler;
use App\Handler\CashTables\GetCashTablePlayersHandler;
use App\Handler\CashTables\GetCashTablesHandler;
use App\Handler\CashTables\LeaveCashTableHandler;
use App\Security\Voter\TableVoter;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/cash-tables')]
class CashTablesController extends BaseApiController
{
    #[Route(path: '', name: 'cash_tables', methods: [Request::METHOD_GET])]
    public function list(
        Request $request,
        GetCashTablesHandler $getCashTablesHandler
    ): JsonResponse {
        try {
            $response = $getCashTablesHandler($request);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tableSetting}', name: 'cash_table_details', methods: [Request::METHOD_GET])]
    public function details(
        ?TableSetting $tableSetting,
        GetCashTableDetailsHandler $getCashTableInfoHandler
    ): JsonResponse {
        try {
            $response = $getCashTableInfoHandler($tableSetting);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tableSetting}/players', name: 'cash_table_players', methods: [Request::METHOD_GET])]
    public function players(
        ?TableSetting $tableSetting,
        GetCashTablePlayersHandler $getCashTablePlayersHandler
    ): JsonResponse {
        try {
            $response = $getCashTablePlayersHandler($tableSetting);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tableSetting}/connect', name: 'sit_table', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted(TableVoter::CONNECT_TO_TABLE, 'tableSetting')]
    public function connect(
        ?TableSetting $tableSetting,
        Request $request,
        ConnectCashTableHandler $connectCashTableHandler
    ): JsonResponse {
        try {
            $response = $connectCashTableHandler($request, $tableSetting);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response(['tableId' => $response]);
    }

    #[Route(path: '/{table}/leave', name: 'leave_table', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted(TableVoter::IS_CASH_TABLE, 'table')]
    #[IsGranted(TableVoter::CHECK_PLAYER_PARTICIPATION, 'table')]
    public function leave(
        Table $table,
        LeaveCashTableHandler $leaveCashTableHandler
    ): JsonResponse {
        try {
            $response = $leaveCashTableHandler($table);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }
}
