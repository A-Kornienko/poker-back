<?php

declare(strict_types=1);

namespace App\Handler\PlayerSetting;

use App\Entity\PlayerSetting;
use App\Enum\StackViewCurrency;
use App\Handler\AbstractHandler;
use App\Response\PlayerSettingResponse;
use App\Service\PlayerSettingService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SwitchStackViewHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected PlayerSettingService $playerSettingService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(): array
    {
        /** @var PlayerSetting $playerSetting */
        $playerSetting = $this->security->getUser()?->getPlayerSetting();

        $stackView = $playerSetting->getStackView();

        if ($stackView->getCurrency() === $stackView->getValue()) {
            $stackView->setValue(StackViewCurrency::Dollar);
        } else {
            $stackView->setValue($stackView->getCurrency());
        }

        $playerSetting = $this->playerSettingService->updatePlayerSetting(
            $playerSetting,
            ['stackView' => $stackView->toArray()]
        );

        return PlayerSettingResponse::item($playerSetting);
    }
}
