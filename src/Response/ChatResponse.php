<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Chat;
use App\Entity\User;

class ChatResponse
{
    public static function collection(array $messages, ?User $user): array
    {
        $response = [];

        /** @var Chat $message */
        foreach ($messages as $message) {
            $response[$message->getId()] = [
                'message'       => $message->getMessage(),
                'id'            => $message->getId(),
                'user'          => $message->getUser()->getLogin(),
                'avatar'        => $message->getUser()->getAvatar(),
                'isCurrentUser' => $user && $user->getId() === $message->getUser()->getId(),
            ];
        }

        return $response;
    }

    public static function paginatedCollection(
        int $page,
        int $limit,
        array $items,
        int $total,
        ?User $user
    ): array {
        if (!$items) {
            return [
                'items'      => [],
                'pagination' => [
                    'total' => 0,
                    'page'  => 0,
                    'limit' => 0,
                    'pages' => 0
                ],
            ];
        }

        return [
            'items'      => ChatResponse::collection($items, $user),
            'pagination' => [
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
        ];
    }
}
