<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\PlayerSettingRepository;
use App\ValueObject\ButtonMacros;
use App\ValueObject\StackView;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerSettingRepository::class)]
class PlayerSetting
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: TYPES::INTEGER)]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'playerSetting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(name: "stack_view", type: Types::JSON, nullable: false)]
    private array $stackView = [];

    #[ORM\Column(name: "card_squeeze", type: Types::BOOLEAN, nullable: true, options: ["default" => false])]
    private bool $cardSqueeze = false;

    #[ORM\Column(name: "button_macros", type: Types::JSON, nullable: false)]
    private array $buttonMacros = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStackView(): StackView
    {
        if (!$this->stackView) {
            return new StackView();
        }

        return (new StackView())->fromArray($this->stackView);
    }

    public function setStackView(?StackView $stackView): static
    {
        if (!$stackView) {
            $this->stackView = (new StackView())->toArray();

            return $this;
        }

        $this->stackView = $stackView->toArray();

        return $this;
    }

    public function getCardSqueeze(): bool
    {
        return $this->cardSqueeze;
    }

    public function setCardSqueeze(bool $cardSqueeze): static
    {
        $this->cardSqueeze = $cardSqueeze;

        return $this;
    }

    public function getButtonMacros(): ButtonMacros
    {
        $buttonMacros = new ButtonMacros();

        if (!$this->buttonMacros) {
            return $buttonMacros;
        }

        return $buttonMacros->fromArray($this->buttonMacros);
    }

    public function setButtonMacros(?ButtonMacros $buttonMacros): static
    {
        if (!$buttonMacros) {
            $this->buttonMacros = [];

            return $this;
        }

        $this->buttonMacros = $buttonMacros->toArray();

        return $this;
    }
}
