<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\{Rules, TableType};
use App\Repository\TableSettingRepository;
use App\ValueObject\TimeBank;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableSettingRepository::class)]
#[ORM\Table(name: '`table_setting`')]
#[ORM\HasLifecycleCallbacks]
class TableSetting
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, length: 64, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: "image", type: Types::STRING, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: "currency", type: Types::STRING, length: 15, options: ["default" => 'USD'])]
    private ?string $currency = null;

    #[ORM\Column(name: "type", type: Types::STRING, length: 20, enumType: TableType::class, options: ["default" => TableType::Cash])]
    private ?TableType $type = null;

    #[ORM\Column(name: "buy_in", type: Types::DECIMAL, options: ["default" => 0])]
    private ?float $buyIn = 0;

    #[ORM\Column(name: "small_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.1])]
    private ?float $smallBlind = 0.1;

    #[ORM\Column(name: "big_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.2])]
    private ?float $bigBlind = 0.2;

    #[ORM\Column(name: "style", type: Types::STRING, length: 20, options: ["default" => null])]
    private ?string $style = null;

    #[ORM\Column(name: "count_players", type: Types::INTEGER, options: ["default" => 10])]
    private ?int $countPlayers = 10;

    #[ORM\Column(name: "rule", type: Types::STRING, enumType: Rules::class, options: ["default" => Rules::TexasHoldem])]
    private ?Rules $rule = null;

    // Процент рейка, применяемый к сумме банка
    #[ORM\Column(name: "rake", type: Types::FLOAT, options: ["default" => 0.05])]
    private ?float $rake = 0.05;

    // Максимальная сума рейка для банка
    #[ORM\Column(name: "rake_cap", type: Types::FLOAT, options: ["default" => 3])]
    private ?float $rakeCap = 3;

    #[ORM\Column(name: "turn_time", type: Types::INTEGER, options: ["default" => 60])]
    private ?int $turnTime = 60;

    #[ORM\Column(name: "time_bank", type: Types::JSON, nullable: true)]
    private ?array $timeBank = [];

    #[ORM\Column(name: "count_cards", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $countCards = null;

    #[ORM\OneToMany(targetEntity: Table::class, mappedBy: 'setting', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getBuyIn(): ?float
    {
        return $this->buyIn;
    }

    public function setBuyIn(?float $buyIn): static
    {
        $this->buyIn = $buyIn;

        return $this;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType(): ?TableType
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param mixed $type
     * @return  self
     */
    public function setType(?TableType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFormattedRule(): ?string
    {
        return $this->getRule()?->value;
    }

    /**
     * @deprecated Use TableSetting::getFormattedRule() instead.
     * @param mixed $rule
     */
    public function setFormattedRule($rule): static
    {
        $this->setRule(Rules::tryFrom($rule));

        return $this;
    }

    public function getFormattedType(): ?string
    {
        return $this->getType()?->value;
    }

    /**
     * @deprecated Use TableSetting::setFormattedType() instead.
     * @param mixed $type
     */
    public function setFormattedType($type): static
    {
        $this->setType(TableType::tryFrom($type));

        return $this;
    }

    public function getSmallBlind(): ?float
    {
        return $this->smallBlind;
    }

    public function setSmallBlind(?float $smallBlind): static
    {
        $this->smallBlind = $smallBlind;

        return $this;
    }

    public function getBigBlind(): ?float
    {
        return $this->bigBlind;
    }

    public function setBigBlind(?float $bigBlind): static
    {
        $this->bigBlind = $bigBlind;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function getCountPlayers(): ?int
    {
        return $this->countPlayers;
    }

    public function setCountPlayers(?int $countPlayers): static
    {
        $this->countPlayers = $countPlayers;

        return $this;
    }

    public function getRule(): ?Rules
    {
        return $this->rule;
    }

    public function setRule(?Rules $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function getTimeBank(): TimeBank
    {
        $timeBank = new TimeBank();

        return $this->timeBank ? $timeBank->fromArray($this->timeBank) : $timeBank;
    }

    public function setTimeBank(TimeBank $timeBank): static
    {
        $this->timeBank = $timeBank->toArray();

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

    public function getRakeCap(): ?float
    {
        return $this->rakeCap;
    }

    public function setRakeCap(?float $rakeCap): static
    {
        $this->rakeCap = $rakeCap;

        return $this;
    }

    public function getTurnTime(): ?int
    {
        return $this->turnTime;
    }

    public function setTurnTime(?int $turnTime): static
    {
        $this->turnTime = $turnTime;

        return $this;
    }

    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function setTableUsers(Collection $tables): static
    {
        $this->tables = $tables;

        return $this;
    }

    public function getCountCards()
    {
        return $this->countCards;
    }

    public function setCountCards(?int $countCards)
    {
        $this->countCards = $countCards;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
