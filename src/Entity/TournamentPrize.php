<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TournamentPrizeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentPrizeRepository::class)]
class TournamentPrize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'prizes')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', nullable: false)]
    private Tournament $tournament;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prizes')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $winner = null;

    #[ORM\Column(name: "sum", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private ?float $sum = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

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

    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function setWinner(?User $winner): static
    {
        $this->winner = $winner;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(?float $sum): static
    {
        $this->sum = $sum;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getSum();
    }
}
