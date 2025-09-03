<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\BankStatus;
use App\Repository\BankRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankRepository::class)]
#[ORM\Table(name: '`bank`')]
#[ORM\HasLifecycleCallbacks]
class Bank
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: TYPES::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Table::class, inversedBy: 'banks')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'banks')]
    private Collection $users;

    #[ORM\Column(name: 'bet', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?float $bet = null;

    #[ORM\Column(name: 'sum', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?float $sum = null;

    #[ORM\Column(name: 'status', enumType: BankStatus::class, type: Types::STRING, options: ['default' => BankStatus::InProgress->value])]
    private ?BankStatus $status = null;

    #[ORM\Column(name: "session", type: Types::STRING, nullable: true)]
    private ?string $session = null;

    #[ORM\Column(name: "rake", type: Types::FLOAT, options: ["default" => 0])]
    private ?float $rake = 0;

    #[ORM\OneToMany(targetEntity: Winner::class, mappedBy: 'bank')]
    private Collection $winners;

    public function __construct()
    {
        $this->winners = new ArrayCollection();
        $this->users   = new ArrayCollection();
    }

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

    public function getBet(): ?float
    {
        return $this->bet;
    }

    public function setBet(?float $bet = 0): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(?float $sum = 0): static
    {
        $this->sum = $sum;

        return $this;
    }

    public function addSum(?float $sum = 0): static
    {
        $this->sum += $sum;

        return $this;
    }

    public function getStatus(): ?BankStatus
    {
        return $this->status;
    }

    public function getFormattedStatus()
    {
        return $this->status ? $this->status->value : null;
    }

    public function setStatus(?BankStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function addUser(User $user): static
    {
        $this->users->add($user);

        return $this;
    }

    public function removeUser(User $user): static
    {
        foreach ($this->users as $bankUser) {
            if ($bankUser->getId() === $user->getId()) {
                $this->users->removeElement($user);
                break;
            }
        }

        return $this;
    }

    public function isUserApplicable(User $user): bool
    {
        foreach ($this->users as $bankUser) {
            if ($bankUser->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
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

    public function getWinners(): Collection
    {
        return $this->winners;
    }

    public function setWinners(Collection $winners): static
    {
        $this->winners = $winners;

        return $this;
    }

    public function getRake(): ?float
    {
        return $this->rake;
    }

    public function setRake(?float $rake): static
    {
        $this->rake = $rake;

        return $this;
    }
}
