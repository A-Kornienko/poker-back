<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\TableUserInvoiceStatus;
use App\Repository\TableUserInvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableUserInvoiceRepository::class)]
#[ORM\Table(name: '`table_user_invoice`')]
#[ORM\HasLifecycleCallbacks]
class TableUserInvoice
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "sum", type: Types::DECIMAL, precision: 10, scale:2, options: ["default" => 0])]
    private float $sum = 0;

    #[ORM\Column(name: "status", type: Types::STRING, enumType: TableUserInvoiceStatus::class, length: 255, options: ["default" => TableUserInvoiceStatus::Pending])]
    private TableUserInvoiceStatus $status = TableUserInvoiceStatus::Pending;

    #[ORM\ManyToOne(inversedBy: 'tableUserInvoices')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\ManyToOne(inversedBy: 'tableUserInvoices')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): static
    {
        $this->sum = $sum;

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

    public function getStatus(): TableUserInvoiceStatus
    {
        return $this->status;
    }

    public function getFormattedStatus(): ?string
    {
        return $this->status ? $this->status->value : null;
    }

    public function setFormattedStatus($status): static
    {
        $this->type = TableUserInvoiceStatus::tryFrom($status);

        return $this;
    }

    public function setStatus(TableUserInvoiceStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
