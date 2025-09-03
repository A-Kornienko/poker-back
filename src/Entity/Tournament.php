<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\{TournamentStatus};
use App\Repository\TournamentRepository;
use App\ValueObject\BreakSettings;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Tournament
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, nullable: false)]
    private string $name;

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(name: "image", type: Types::STRING, nullable: true)]
    private ?string $image = null;

    // Дата и время начала турнира.
    #[ORM\Column(name: "date_start", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $dateStart = 0;

    #[ORM\Column(name: "small_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.1])]
    private ?float $smallBlind = 0.1;

    #[ORM\Column(name: "big_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.2])]
    private ?float $bigBlind = 0.2;

    #[ORM\Column(name: "last_blind_update", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $lastBlindUpdate = 0;

    #[ORM\Column(name: "balance", type: Types::DECIMAL, precision: 10, scale:2, options: ["default" => 0])]
    private float $balance = 0;

    #[ORM\Column(name: "status", type: Types::STRING, enumType: TournamentStatus::class, options: ["default" => TournamentStatus::Pending])]
    private TournamentStatus $status = TournamentStatus::Pending;

    // Время начала регистрации на турнир
    #[ORM\Column(name: "date_start_registration", type: Types::INTEGER, options: ["default" => 0])]
    private int $dateStartRegistration = 0;

    // Время окончания регистрации на турнир
    #[ORM\Column(name: "date_end_registration", type: Types::INTEGER, options: ["default" => 0])]
    private int $dateEndRegistration = 0;

    // Возможность автоповтора турнира
    #[ORM\Column(name: "autorepeat", type: Types::BOOLEAN, options: ["default" => false])]
    private bool $autorepeat = false;

    // Время создания нового турнира после завершения текущего
    #[ORM\Column(name: "autorepeat_date", type: Types::INTEGER, nullable: true)]
    private ?int $autorepeatDate = null;

    #[ORM\Column(name: "blind_level", type: Types::INTEGER, options: ["default" => 1])]
    private int $blindLevel = 1;

    #[ORM\OneToMany(targetEntity: TournamentUser::class, mappedBy: 'tournament', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tournamentUsers;

    #[ORM\OneToMany(targetEntity: Table::class, mappedBy: 'tournament', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tables;

    #[ORM\OneToMany(targetEntity: TournamentPrize::class, mappedBy: 'tournament', cascade: ['persist'], orphanRemoval: true)]
    private Collection $prizes;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'tournament', cascade: ['persist'], orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\ManyToOne(targetEntity: TournamentSetting::class, inversedBy: 'tournaments')]
    #[ORM\JoinColumn(name: 'setting_id', referencedColumnName: 'id')]
    private ?TournamentSetting $setting;

    public function __construct()
    {
        $this->tournamentUsers = new ArrayCollection();
        $this->tables          = new ArrayCollection();
        $this->prizes          = new ArrayCollection();
        $this->notifications   = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDateStart(): ?int
    {
        return $this->dateStart;
    }

    public function getFormattedDateStart(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->dateStart ?? 0);
    }

    public function setFormattedDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart->getTimestamp();

        return $this;
    }

    public function getFormattedDateStartRegistration(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->dateStartRegistration ?? 0);
    }

    public function setFormattedDateStartRegistration(\DateTimeInterface $dateStartRegistration): static
    {
        $this->dateStartRegistration = $dateStartRegistration->getTimestamp();

        return $this;
    }

    public function getFormattedDateEndRegistration(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->dateEndRegistration ?? 0);
    }

    public function setFormattedDateEndRegistration(\DateTimeInterface $dateEndRegistration): static
    {
        $this->dateEndRegistration = $dateEndRegistration->getTimestamp();

        return $this;
    }

    public function setDateStart(?int $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTournamentUsers(): Collection
    {
        return $this->tournamentUsers;
    }

    public function addTournamentUser(TournamentUser $tournamentUser): static
    {
        $this->tournamentUsers->add($tournamentUser);

        return $this;
    }

    public function removeTournamentUser(TournamentUser $tournamentUser): static
    {
        foreach ($this->tournamentUsers as $key => $existedTournamentUser) {
            if ($existedTournamentUser->getId() === $tournamentUser->getId()) {
                $this->tournamentUsers->remove($key);
                break;
            }
        }

        return $this;
    }

    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function setTables(Collection $tables): static
    {
        $this->tables = $tables;

        return $this;
    }

    public function addTable(Table $table): static
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->setTournament($this);
        }

        return $this;
    }

    public function removeTable(Table $table): static
    {
        if ($this->tables->removeElement($table)) {
            if ($table->getTournament() === $this) {
                $table->setTournament(null);
            }
        }

        return $this;
    }

    public function getPrizes(): Collection
    {
        return $this->prizes;
    }

    public function setPrizes(Collection $prizes): static
    {
        $this->prizes = $prizes;

        return $this;
    }

    public function addPrize(TournamentPrize $prize): static
    {
        if (!$this->prizes->contains($prize)) {
            $this->prizes->add($prize);
            $prize->setTournament($this);
        }

        return $this;
    }

    public function removePrize(TournamentPrize $prize): static
    {
        if ($this->prizes->removeElement($prize)) {
            if ($prize->getTournament() === $this) {
                $prize->setTournament(null);
            }
        }

        return $this;
    }

    public function getStatus(): TournamentStatus
    {
        return $this->status;
    }

    public function getFormattedStatus(): ?string
    {
        return $this->status?->value;
    }

    public function setFormattedStatus($status): static
    {
        $this->status = TournamentStatus::tryFrom($status);

        return $this;
    }

    public function setStatus(TournamentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateStartRegistration(): int
    {
        return $this->dateStartRegistration;
    }

    public function setDateStartRegistration(int $dateStartRegistration): static
    {
        $this->dateStartRegistration = $dateStartRegistration;

        return $this;
    }

    public function getDateEndRegistration(): int
    {
        return $this->dateEndRegistration;
    }

    public function setDateEndRegistration(int $dateEndRegistration): static
    {
        $this->dateEndRegistration = $dateEndRegistration;

        return $this;
    }

    /**
     * @deprecated Use TournamentSetting::getLimitMembers() instead.
     */
    public function getLimitMembers(): ?int
    {
        return $this->setting->getLimitMembers();
    }

    public function getFormattedBreakSettings(): BreakSettings
    {
        return new BreakSettings();
    }

    public function getLastBlindUpdate(): ?int
    {
        return $this->lastBlindUpdate;
    }

    public function setLastBlindUpdate(?int $lastBlindUpdate): static
    {
        $this->lastBlindUpdate = $lastBlindUpdate;

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

    public function getAutorepeat(): bool
    {
        return $this->autorepeat;
    }

    public function setAutorepeat(bool $autorepeat): static
    {
        $this->autorepeat = $autorepeat;

        return $this;
    }

    public function getAutorepeatDate(): ?int
    {
        return $this->autorepeatDate;
    }

    public function setAutorepeatDate(?int $autorepeatDate): static
    {
        $this->autorepeatDate = $autorepeatDate;

        return $this;
    }

    public function getNotifications(): ?Collection
    {
        return $this->notifications;
    }

    public function setNotifications(Collection $notifications): static
    {
        $this->notifications = $notifications;

        return $this;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setTournament($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getTournament() === $this) {
                $notification->setTournament(null);
            }
        }

        return $this;
    }

    public function getSetting(): ?TournamentSetting
    {
        return $this->setting;
    }

    public function setSetting(TournamentSetting $tournamentSetting): static
    {
        $this->setting = $tournamentSetting;

        return $this;
    }

    public function getBlindLevel(): int
    {
        return $this->blindLevel;
    }

    public function setBlindLevel(int $blindLevel): static
    {
        $this->blindLevel = $blindLevel;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
