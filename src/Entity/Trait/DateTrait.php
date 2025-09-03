<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait DateTrait
{
    #[ORM\Column(name: "created_at", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $createdAt = 0;

    #[ORM\Column(name: "updated_at", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $updatedAt = 0;

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function getFormattedCreatedAt(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->createdAt ?? 0);
    }

    public function setCreatedAt(?int $createdAt = 0): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function getFormattedUpdatedAt(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->updatedAt ?? 0);
    }

    public function setUpdatedAt(?int $updatedAt = 0): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = time();
        $this->updatedAt = time();
    }

    #[ORM\PreUpdate]
    public function setUpdatedValue(): void
    {
        $this->updatedAt = time();
    }
}
