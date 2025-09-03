<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Chat, Table, User};
use App\Repository\{ChatRepository, TableRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChatService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TableRepository $tableRepository,
        protected ChatRepository $chatRepository,
        protected TranslatorInterface $translator
    ) {
    }

    public function create(Table $table, string $message, ?User $user): void
    {
        $newChatMessage = new Chat();
        $newChatMessage->setUser($user)
            ->setTable($table)
            ->setMessage($message)
            ->setCreatedAt(time());

        $this->entityManager->persist($newChatMessage);
        $this->entityManager->flush();
    }
}
