<?php

declare(strict_types=1);

namespace App\Handler\CashTables;

use App\Entity\TableSetting;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Response\TableSettingsResponse;

class GetCashTableDetailsHandler extends AbstractHandler
{
    public function __invoke(?TableSetting $tableSetting = null): array
    {
        if (!$tableSetting) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_SETTINGS);
        }

        return TableSettingsResponse::details($tableSetting);
    }
}
