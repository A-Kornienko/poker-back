<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{PlayerSetting};
use App\ValueObject\ButtonMacros;
use App\ValueObject\StackView;
use Doctrine\ORM\EntityManagerInterface;

class PlayerSettingService
{
    public const STACK_VIEW    = 'stackView';
    public const CART_SQUEEZE  = 'cardSqueeze';
    public const BUTTON_MACROS = 'buttonMacros';

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function updatePlayerSetting(PlayerSetting $playerSetting, array $setting): PlayerSetting
    {
        if (array_key_exists(static::STACK_VIEW, $setting)) {
            $stackView = (new StackView());
            $stackView->fromArray($setting[static::STACK_VIEW]);

            $playerSetting->setStackView($stackView);
        }

        if (array_key_exists(static::CART_SQUEEZE, $setting)) {
            $playerSetting->setCardSqueeze((bool) $setting[static::CART_SQUEEZE]);
        }

        if (array_key_exists(static::BUTTON_MACROS, $setting)) {
            $buttonMacros = (new ButtonMacros())->fromArray($setting[static::BUTTON_MACROS]);
            $playerSetting->setButtonMacros($buttonMacros);
        }

        $this->entityManager->persist($playerSetting);
        $this->entityManager->flush();

        return $playerSetting;
    }
}
