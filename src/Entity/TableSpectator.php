<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SpectatorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpectatorRepository::class)]
#[ORM\Table(name: '`spectator`')]
#[ORM\HasLifecycleCallbacks]
class TableSpectator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tableSpectators')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\Column(name: "uuid", type: Types::STRING, length: 255)]
    private string $uuid;

    #[ORM\Column(name: "expiration_time", type: Types::INTEGER, nullable: true)]
    private ?int $expirationTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

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

    public function getUuId(): string
    {
        return $this->uuid;
    }

    public function setUuId(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    public function setExpirationTime(?int $expirationTime): static
    {
        $this->expirationTime = $expirationTime;

        return $this;
    }
}
