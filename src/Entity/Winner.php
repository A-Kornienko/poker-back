<?php

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\WinnerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WinnerRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Winner
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'winners')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'winners')]
    #[ORM\JoinColumn(name: 'table_user_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private TableUser $tableUser;

    #[ORM\ManyToOne(inversedBy: 'winners')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\ManyToOne(inversedBy: 'winners')]
    #[ORM\JoinColumn(name: 'bank_id', referencedColumnName: 'id')]
    private Bank $bank;

    #[ORM\Column(name: 'sum', type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $sum;

    #[ORM\Column(name: "session", type: Types::STRING, nullable: true)]
    private ?string $session = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getBank(): Bank
    {
        return $this->bank;
    }

    public function setBank(Bank $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function setSum(float $sum): static
    {
        $this->sum = $sum;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getTableUser(): TableUser
    {
        return $this->tableUser;
    }

    public function setTableUser(TableUser $tableUser): static
    {
        $this->tableUser = $tableUser;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getUser()->getLogin();
    }
}
