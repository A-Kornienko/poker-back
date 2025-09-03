<?php

namespace App\Entity;

use App\Repository\TournamentUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentUserRepository::class)]
#[ORM\Table(name: '`tournament_user`', uniqueConstraints: [
    new ORM\UniqueConstraint(columns: ['tournament_id', 'rank'])
])]
class TournamentUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tournamentUsers')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id')]
    private Tournament $tournament;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tournamentUsers')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Table::class, inversedBy: 'tournamentUsers')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private ?Table $table = null;

    #[ORM\Column(name: '`rank`', type: TYPES::INTEGER, nullable: true)]
    private ?int $rank = null;

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function setTournament(Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(Table $table): self
    {
        $this->table = $table;

        return $this;
    }
}
