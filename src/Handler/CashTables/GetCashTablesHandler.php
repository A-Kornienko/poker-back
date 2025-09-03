<?php

declare(strict_types=1);

namespace App\Handler\CashTables;

use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableSettingRepository;
use App\Response\TableSettingsResponse;
use App\Service\TableSettingService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetCashTablesHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected TableSettingRepository $tableSettingRepository,
        protected TableSettingService $tableSettingService
    ) {
        parent::__construct($security, $translator);
    }

    public function __invoke(Request $request): array
    {
        $page  = $request->query->getInt(static::REQUEST_PAGE, 1);
        $limit = $request->query->getInt(static::REQUEST_LIMIT, 20);
        $rule  = $request->query->getString(static::REQUEST_RULE);

        $tableSettingsData = $this->tableSettingService->getCashCollection($page, $limit, $rule);
        $tableSettings     = $tableSettingsData['items'];
        $totalRecords      = $tableSettingsData['total'];

        if (!$tableSettings) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::NO_SETTINGS);
        }

        return [
            'items'      => TableSettingsResponse::collection(...$tableSettings),
            'pagination' => [
                'total' => $totalRecords,
                'page'  => $page,
                'limit' => $limit,
                'pages' => ceil($totalRecords / $limit),
            ],
        ];
    }
}
