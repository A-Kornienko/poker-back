<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\UserNotificationType;
use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
#[ORM\Table(name: '`notification`')]
class Notification
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "message", type: Types::STRING, length: 255, nullable: false)]
    private string $message;

    #[ORM\Column(name: "type", type: Types::STRING, length: 20, enumType: UserNotificationType::class, options: ["default" => null])]
    private ?UserNotificationType $type = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id')]
    private Tournament $tournament;

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

    public function getType(): ?UserNotificationType
    {
        return $this->type;
    }

    public function setType(?UserNotificationType $type): static
    {
        $this->type = $type;

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

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function setTournament(Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getFormattedType(): ?string
    {
        return $this->type ? $this->type->value : null;
    }
}
