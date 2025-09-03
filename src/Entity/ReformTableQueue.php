<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ReformTableQueueStatus;
use App\Repository\ReformTableQueueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReformTableQueueRepository::class)]
#[ORM\Table(name: "reform_table_queues", uniqueConstraints: [
    new ORM\UniqueConstraint(name: "unique_table_session", columns: ["table_id", "table_session_id"])
])]
class ReformTableQueue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "table_session", type: Types::STRING, nullable: false)]
    private ?string $tableSession = null;

    #[ORM\ManyToOne(targetEntity: Table::class, inversedBy: 'reformTableQueue')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id', nullable: false)]
    private Table $table;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'reform_table_queue')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', nullable: false)]
    private Tournament $tournament;

    #[ORM\Column(name: "status", type: Types::STRING, nullable: false, enumType: ReformTableQueueStatus::class, options: ["default" => ReformTableQueueStatus::Pending->value])]
    private ?ReformTableQueueStatus $status = ReformTableQueueStatus::Pending;

    #[ORM\Column(name: "data", type: Types::JSON, nullable: true)]
    private ?array $data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTableSession(): ?string
    {
        return $this->tableSession;
    }

    public function setTableSession(?string $tableSession): static
    {
        $this->tableSession = $tableSession;

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

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getStatus(): ?ReformTableQueueStatus
    {
        return $this->status;
    }

    public function setStatus(ReformTableQueueStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
