<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
#[ORM\Table(name: '`chat`')]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "message", type: Types::STRING, length: 255, nullable: false)]
    private string $message;

    #[ORM\Column(name: "created_at", type: Types::INTEGER, nullable: false)]
    private int $createdAt;

    #[ORM\ManyToOne(targetEntity: Table::class, inversedBy: 'chats')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'chats')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
