<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\TableState;
use App\Handler\TableState\GetTableStateHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request};
use Symfony\Component\Routing\Attribute\Route;

class TableStateController extends BaseApiController
{
    #[Route(path: '/api/table-state/{table}', name: 'sse_table_state', methods: [Request::METHOD_GET])]
    public function state(
        Table $table,
        GetTableStateHandler $getTableStateHandler,
        EntityManagerInterface $entityManager
    ) {
        if (ob_get_level()) {
            ob_end_clean();
        }

        return $this->streamedResponse(function () use (
            $getTableStateHandler,
            $table,
            $entityManager
        ) {
            while(true) {
                $entityManager->clear();
                if (!ob_get_level()) {
                    ob_start();
                }

                $state = $getTableStateHandler($table, $this->security->getUser());
                $state['isAuthorized'] = (bool) $this->security->getUser();
                echo "data: " . json_encode($state) . "\n\n";

                ob_flush();
                flush();

                if ($state['state'] === TableState::Init->value) {
                    break;
                }

                unset($state);
                ob_end_clean();
                gc_collect_cycles();

                sleep(1);
            }
        })->send();
    }
}
