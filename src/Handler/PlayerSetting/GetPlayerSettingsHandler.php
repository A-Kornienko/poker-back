<?php

declare(strict_types=1);

namespace App\Handler\PlayerSetting;

use App\Entity\PlayerSetting;
use App\Handler\AbstractHandler;
use App\Response\PlayerSettingResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetPlayerSettingsHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(): array
    {
        $user = $this->security->getUser();

        return PlayerSettingResponse::item(
            $user->getPlayerSetting() ?? (new PlayerSetting())->setUser($user)
        );
    }
}
