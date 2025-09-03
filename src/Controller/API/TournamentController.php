<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Tournament;
use App\Enum\UserRole;
use App\Handler\Tournaments\CancelTournamentRegistrationHandler;
use App\Handler\Tournaments\GetTournamentBlindsStructureHandler;
use App\Handler\Tournaments\GetTournamentLobbyHandler;
use App\Handler\Tournaments\GetTournamentPlayersHandler;
use App\Handler\Tournaments\GetTournamentPrizesHandler;
use App\Handler\Tournaments\GetTournamentsHandler;
use App\Handler\Tournaments\GetTournamentShortInfoHandler;
use App\Handler\Tournaments\GetTournamentTablesHandler;
use App\Handler\Tournaments\RegistrationTournamentHandler;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/tournaments')]
class TournamentController extends BaseApiController
{
    #[Route(path: '', name: 'tournaments', methods: [Request::METHOD_GET])]
    public function list(
        Request $request,
        GetTournamentsHandler $getTournamentTablesHandler
    ): JsonResponse {
        try {
            $response = $getTournamentTablesHandler($request);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}', name: 'tournament_details', methods: [Request::METHOD_GET])]
    public function shortDetails(
        ?Tournament $tournament,
        GetTournamentShortInfoHandler $getShortInfoTournamentHandler
    ): JsonResponse {
        try {
            $response = $getShortInfoTournamentHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/lobby', name: 'tournament_lobby', methods: [Request::METHOD_GET])]
    public function lobby(
        ?Tournament $tournament,
        GetTournamentLobbyHandler $getTournamentLobby
    ): JsonResponse {
        try {
            $response = $getTournamentLobby($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/players', name: 'tournament_players', methods: [Request::METHOD_GET])]
    public function players(
        ?Tournament $tournament,
        GetTournamentPlayersHandler $getPlayerInfoTournamentHandler
    ): JsonResponse {
        try {
            $response = $getPlayerInfoTournamentHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/tables', name: 'tournament_tables', methods: [Request::METHOD_GET])]
    public function tables(
        Request $request,
        ?Tournament $tournament,
        GetTournamentTablesHandler $getTournamentTablesHandler
    ): JsonResponse {
        try {
            $response = $getTournamentTablesHandler($request, $tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/prizes', name: 'tournament_prizes', methods: [Request::METHOD_GET])]
    public function prize(
        ?Tournament $tournament,
        GetTournamentPrizesHandler $getTournamentPrizesHandler
    ): JsonResponse {
        try {
            $response = $getTournamentPrizesHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/blinds-structure', name: 'tournament_blinds_structure', methods: [Request::METHOD_GET])]
    public function structure(
        ?Tournament $tournament,
        GetTournamentBlindsStructureHandler $getTournamentStructureHandler
    ): JsonResponse {
        try {
            $response = $getTournamentStructureHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '/{tournament}/registration', name: 'tournament_registration', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    public function registration(
        ?Tournament $tournament,
        RegistrationTournamentHandler $tournamentRegistrationHandler
    ): JsonResponse {
        try {
            $tournamentRegistrationHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }

    #[Route(path: '/{tournament}/cancel-registration', name: 'tournament_cancel_registration', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    public function cancelRegistration(
        ?Tournament $tournament,
        CancelTournamentRegistrationHandler $tournamentCancelRegistrationHandler
    ): JsonResponse {
        try {
            $tournamentCancelRegistrationHandler($tournament);
        } catch (\Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response([], status: 201);
    }
}
