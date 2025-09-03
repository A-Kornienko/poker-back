<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Enum\UserRole;
use App\Handler\PlayerSetting\GetPlayerSettingsHandler;
use App\Handler\PlayerSetting\SwitchStackViewHandler;
use App\Handler\PlayerSetting\UpdatePlayerSettingHandler;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/player-setting')]
class PlayerSettingController extends BaseApiController
{
    #[Route(path: '', name: 'player_setting_details', methods: [Request::METHOD_GET])]
    #[IsGranted(UserRole::Player->value)]
    public function getPlayerSetting(
        GetPlayerSettingsHandler $playerSettingsHandler
    ): JsonResponse {
        try {
            $response = $playerSettingsHandler();
        } catch (Exception $exception) {
            $response = $this->response([], $exception->getCode());
        }

        return $this->response($response);
    }

    #[Route(path: '', name: 'player_setting_update', methods: [Request::METHOD_PUT])]
    #[IsGranted(UserRole::Player->value)]
    public function putPlayerSetting(
        Request $request,
        UpdatePlayerSettingHandler $updatePlayerSettingHandler
    ): JsonResponse {
        try {
            $response = $updatePlayerSettingHandler($request);
        } catch (Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response(data: $response, status: 201);
    }

    #[Route('/switch-stack-view', name: 'change_stack_view', methods: [Request::METHOD_PATCH])]
    #[IsGranted(UserRole::Player->value)]
    public function switchStackView(
        SwitchStackViewHandler $switchStackViewHandler,
    ): JsonResponse {
        try {
            $response = $switchStackViewHandler();
        } catch (Exception $exception) {
            return $this->response([], $exception->getCode());
        }

        return $this->response(data: $response);
    }
}
