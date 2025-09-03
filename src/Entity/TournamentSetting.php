<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\ValueObject\LateRegistration;
use App\Enum\{Rules, TournamentType};
use App\Repository\TournamentSettingRepository;
use App\ValueObject\BlindSetting;
use App\ValueObject\BreakSettings;
use App\ValueObject\BuyInSettings;
use App\ValueObject\TimeBank;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentSettingRepository::class)]
#[ORM\Table(name: '`tournament_setting`')]
#[ORM\HasLifecycleCallbacks]
class TournamentSetting
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, nullable: false)]
    private string $name;

    #[ORM\Column(name: "type", type: Types::STRING, length: 255, enumType: TournamentType::class, options: ["default" => TournamentType::Paid])]
    private TournamentType $type = TournamentType::Paid;

    // Сумма докупки или входа на турнир.
    #[ORM\Column(name: "entry_sum", type: Types::DECIMAL, precision: 10, scale:2, options: ["default" => 0])]
    private ?float $entrySum = 0;

    #[ORM\Column(name: "entry_chips", type: Types::DECIMAL, precision: 10, scale:2, options: ["default" => 0])]
    private ?float $entryChips = 0;

    // Количество игроков для старта турнира.
    #[ORM\Column(name: "start_count_players", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $startCountPlayers = 0;

    // Настройки перерыва
    #[ORM\Column(name: "break_settings", type: Types::JSON, nullable: true)]
    private ?array $breakSettings = [];

    #[ORM\Column(name: "buy_in_settings", type: Types::JSON, nullable: true)]
    private ?array $buyInSettings = [];

    #[ORM\Column(name: "rule", type: Types::STRING, enumType: Rules::class, options: ["default" => Rules::TexasHoldem])]
    private ?Rules $rule = Rules::TexasHoldem;

    // Количество участников в турнире
    #[ORM\Column(name: "limit_members", type: Types::INTEGER, nullable: true)]
    private ?int $limitMembers = null;

    // Возможность возможность синхронизации столов для призовых мест
    #[ORM\Column(name: "table_synchronization", type: Types::BOOLEAN, options: ["default" => false])]
    private bool $tableSynchronization = false;

    #[ORM\Column(name: "rake", type: Types::FLOAT, options: ["default" => 0.05])]
    private ?float $rake = 0.05;

    #[ORM\Column(name: "min_count_members", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $minCountMembers = null;

    #[ORM\Column(name: "prize_rule", type: Types::TEXT, options: ["default" => ''])]
    private ?string $prizeRule = null;

    #[ORM\Column(name: "turn_time", type: Types::INTEGER, options: ["default" => 60])]
    private ?int $turnTime = 60;

    #[ORM\Column(name: "time_bank", type: Types::JSON, nullable: true)]
    private ?array $timeBank = [];

    #[ORM\Column(name: "blind_setting", type: Types::JSON, nullable: true)]
    private ?array $blindSetting = [];

    #[ORM\Column(name: "late_registration", type: Types::JSON, nullable: true)]
    private ?array $lateRegistration = [];

    #[ORM\OneToMany(targetEntity: Tournament::class, mappedBy: 'setting', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tournaments;

    public function __construct()
    {
        $this->tournaments = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): TournamentType
    {
        return $this->type;
    }

    public function getFormattedType(): ?string
    {
        return $this->type?->value;
    }

    public function setFormattedType($type): static
    {
        $this->type = TournamentType::tryFrom($type);

        return $this;
    }

    public function setType(TournamentType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getEntrySum(): ?float
    {
        return $this->entrySum;
    }

    public function setEntrySum(?float $entrySum): static
    {
        $this->entrySum = $entrySum;

        return $this;
    }

    public function getStartCountPlayers(): ?int
    {
        return $this->startCountPlayers;
    }

    public function setStartCountPlayers(?int $startCountPlayers): static
    {
        $this->startCountPlayers = $startCountPlayers;

        return $this;
    }

    public function getFormattedBlindSpeed(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->blindSpeed ?? 0);
    }

    public function getRule(): Rules
    {
        return $this->rule;
    }

    public function formattedRule(): ?string
    {
        return $this->rule ? $this->rule->value : null;
    }

    public function setFormattedRule($rule): static
    {
        $this->rule = Rules::tryFrom($rule);

        return $this;
    }

    public function setRule(Rules $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function getLimitMembers(): ?int
    {
        return $this->limitMembers;
    }

    public function setLimitMembers(?int $limitMembers): static
    {
        $this->limitMembers = $limitMembers;

        return $this;
    }

    public function getBreakSettings(): BreakSettings
    {
        $breakSetting = new BreakSettings();

        if (!$this->breakSettings) {
            return $breakSetting;
        }

        return $breakSetting->fromArray($this->breakSettings);
    }

    public function setBreakSettings(BreakSettings $breakSettings): static
    {
        $this->breakSettings = $breakSettings->toArray();

        return $this;
    }

    public function getFormattedBreakSettings(): BreakSettings
    {
        return new BreakSettings();
    }

    public function getBuyInSettings(): BuyInSettings
    {
        $buyInSettings = new BuyInSettings();

        if (!$this->buyInSettings) {
            return $buyInSettings;
        }

        return $buyInSettings->fromArray($this->buyInSettings);
    }

    public function setBuyInSettings(?BuyInSettings $buyInSettings): static
    {
        if (!$buyInSettings) {
            $this->buyInSettings = [];

            return $this;
        }

        $this->buyInSettings = $buyInSettings->toArray();

        return $this;
    }

    public function getEntryChips(): ?float
    {
        return $this->entryChips;
    }

    public function setEntryChips(?float $entryChips): static
    {
        $this->entryChips = $entryChips;

        return $this;
    }

    public function getMinCountMembers(): ?int
    {
        return $this->minCountMembers;
    }

    public function setMinCountMembers(?int $minCountMembers): static
    {
        $this->minCountMembers = $minCountMembers;

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

    public function getTableSynchronization(): bool
    {
        return $this->tableSynchronization;
    }

    public function setTableSynchronization(bool $tableSynchronization): void
    {
        $this->tableSynchronization = $tableSynchronization;
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

    public function getTimeBank(): TimeBank
    {
        $timeBank = new TimeBank();

        if (!$this->timeBank) {
            return $timeBank;
        }

        return $timeBank->fromArray($this->timeBank);
    }

    public function setTimeBank(TimeBank $timeBank): static
    {
        $this->timeBank = $timeBank->toArray();

        return $this;
    }

    public function getBlindSetting(): BlindSetting
    {
        $blindSetting = new BlindSetting();

        if (!$this->blindSetting) {
            return $blindSetting;
        }

        return $blindSetting->fromArray($this->blindSetting);
    }

    public function setBlindSetting(BlindSetting $blindSetting): static
    {
        $this->blindSetting = $blindSetting->toArray();

        return $this;
    }

    public function getPrizeRule(): ?string
    {
        return $this->prizeRule;
    }

    public function setPrizeRule(?string $prizeRule): static
    {
        $this->prizeRule = $prizeRule;

        return $this;
    }

    public function getTournaments(): Collection
    {
        return $this->tournaments;
    }

    public function getLateRegistration(): LateRegistration
    {
        $lateRegistration = new LateRegistration();

        if (!$this->lateRegistration) {
            return $lateRegistration;
        }

        return $lateRegistration->fromArray($this->lateRegistration);
    }

    public function setLateRegistration(LateRegistration $lateRegistration): static
    {
        $this->lateRegistration = $lateRegistration->toArray();

        return $this;
    }

    public function __toString(): string
    {
        return $this->getRule()->value;
    }
}
