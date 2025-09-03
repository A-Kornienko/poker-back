<?php

declare(strict_types=1);

namespace App\Handler\PlayerSetting;

use App\Entity\PlayerSetting;
use App\Handler\AbstractHandler;
use App\Response\PlayerSettingResponse;
use App\Service\PlayerSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdatePlayerSettingHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected PlayerSettingService $playerSettingService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Request $request): array
    {
        $playerSetting = $this->security->getUser()?->getPlayerSetting() ?? (new PlayerSetting())->setUser($this->security->getUser());

        $playerSettingResponse = $this->playerSettingService->updatePlayerSetting(
            $playerSetting,
            $this->getJsonParam($request)
        );

        return PlayerSettingResponse::item($playerSettingResponse);
    }
}
