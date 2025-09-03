<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\BaseApiController;
use App\Entity\Table;
use App\Enum\UserRole;
use App\Handler\Chat\GetMessagesChatHandler;
use App\Handler\Chat\SendMessagesChatHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/chat')]
class ChatController extends BaseApiController
{
    #[Route(path: '/{table}/history', name: 'history_messages', methods: [Request::METHOD_GET])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function history(
        Table $table,
        Request $request,
        GetMessagesChatHandler $getMessagesChatHandler
    ) {
        $messages = $getMessagesChatHandler($table, $request);

        return $this->response($messages);
    }

    #[Route(path: '/{table}', name: 'sse_get_messages')]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function messages(
        Table $table,
        Request $request,
        GetMessagesChatHandler $getMessagesChatHandler,
        EntityManagerInterface $entityManager,
    ) {
        return $this->streamedResponse(function () use ($getMessagesChatHandler, $table, $request, $entityManager) {
            ob_end_clean();
            $startTime = time();
            while (true) {
                if (!ob_get_level()) {
                    ob_start();
                }

                $messages = $getMessagesChatHandler($table, $request);
                echo "data: " . json_encode($messages) . "\n\n";

                unset($messages);
                gc_collect_cycles();

                ob_flush();
                flush();

                if ((time() - $startTime) >= 600) {
                    $entityManager->clear();
                    ob_end_clean();

                    break;
                }

                sleep(1);
            }
        })->send();
    }

    /**
     * @param Table $table
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: '/{table}/send', name: 'send_message', methods: [Request::METHOD_POST])]
    #[IsGranted(UserRole::Player->value)]
    #[IsGranted('checkPlayerParticipation', 'table')]
    public function send(
        Table $table,
        Request $request,
        SendMessagesChatHandler $sendMessagesChatHandler
    ): JsonResponse {
        try {
            $sendMessagesChatHandler($table, $request);

            return $this->response(data: [], status: 201);
        } catch (\Exception $e) {
            return $this->response(data: ['error' => $e->getMessage()], errorCode: $e->getCode());
        }
    }
}
