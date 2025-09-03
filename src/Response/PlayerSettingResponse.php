<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\PlayerSetting;

class PlayerSettingResponse
{
    public static function item(PlayerSetting $playerSetting): array
    {
        return [
            'stackView'    => $playerSetting->getStackView()->toArray(),
            'cardSqueeze'  => $playerSetting->getCardSqueeze(),
            'buttonMacros' => $playerSetting->getButtonMacros()->toArray()
        ];
    }
}
