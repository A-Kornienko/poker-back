<?php

declare(strict_types=1);

namespace App\Handler\CashTables;

use App\Entity\TableSetting;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Response\PlayerResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetCashTablePlayersHandler extends AbstractHandler
{
    public function __construct(
        protected TableUserRepository $tableUserRepository,
        protected Security $security,
        protected TranslatorInterface $translator
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(?TableSetting $tableSetting = null): array
    {
        if (!$tableSetting) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_SETTINGS);
        }

        $players = $this->tableUserRepository->getCollectionByTableSetting($tableSetting);

        return $players ? PlayerResponse::tableCollection($players) : [];
    }
}
