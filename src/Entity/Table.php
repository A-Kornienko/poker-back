<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\{Round, TableState};
use App\Repository\TableRepository;
use App\ValueObject\Card;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\Table(name: '`table`')]
#[ORM\HasLifecycleCallbacks]
class Table
{
    //TODO поля связанные с настройками стола были перенесены в TableSetting
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, length: 64, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: "max_bet", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private ?float $maxBet = 0;

    #[ORM\Column(name: "session", type: Types::STRING, nullable: true)]
    private ?string $session = null;

    #[ORM\Column(name: "round", type: Types::STRING, enumType: Round::class, options: ["default" => Round::PreFlop->value])]
    private Round $round = Round::PreFlop;

    #[ORM\Column(name: "state", type: Types::STRING, options: ["default" => TableState::Init->value])]
    private string $state = TableState::Init->value;

    #[ORM\Column(name: "dealer_place", type: Types::INTEGER, options: ["default" => 1])]
    private ?int $dealerPlace = 1;

    #[ORM\Column(name: "small_blind_place", type: Types::INTEGER, options: ["default" => 2])]
    private ?int $smallBlindPlace = 2;

    #[ORM\Column(name: "big_blind_place", type: Types::INTEGER, options: ["default" => 3])]
    private ?int $bigBlindPlace = 3;

    #[ORM\Column(name: "turn_place", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $turnPlace = 0;

    #[ORM\Column(name: "last_word", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $lastWordPlace = 0;

    #[ORM\Column(name: "cards", type: Types::JSON, nullable: true)]
    private ?array $cards = null;

    #[ORM\Column(name: "round_expiration_time", type: Types::INTEGER, nullable: true)]
    private ?int $roundExpirationTime = null;

    #[ORM\Column(name: "is_archived", type: Types::BOOLEAN, nullable: true, options: ["default" => false])]
    private bool $isArchived = false;

    #[ORM\Column(name: "rake_status", type: Types::BOOLEAN, options: ["default" => false])]
    private ?bool $rakeStatus = false;

    #[ORM\Column(name: "number", type: Types::INTEGER, options: ["default" => 0])]
    private ?int $number = 0;

    #[ORM\Column(name: "reconnect_time", type: Types::INTEGER, nullable: true)]
    private ?int $reconnectTime = 120;

    #[ORM\Column(name: "small_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.1])]
    private ?float $smallBlind = 0.1;

    #[ORM\Column(name: "big_blind", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0.2])]
    private ?float $bigBlind = 0.2;

    /**
     * @var Collection<int, TableUser>
     */
    #[ORM\OneToMany(targetEntity: TableUser::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tableUsers;

    /**
     * @var Collection<int, TableUserInvoice>
     */
    #[ORM\OneToMany(targetEntity: TableUserInvoice::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tableUserInvoices;

    /**
     * @var Collection<int, Chat>
     */
    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $chats;

    /**
     * @var Collection<int, TableHistory>
     */
    #[ORM\OneToMany(targetEntity: TableHistory::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tableHistory;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tables')]
    #[ORM\JoinColumn(name: 'tournament_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Tournament $tournament = null;

    #[ORM\OneToMany(targetEntity: TournamentUser::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tournamentUsers;

    #[ORM\OneToMany(targetEntity: TableSpectator::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $spectators;

    #[ORM\OneToMany(targetEntity: Bank::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $banks;

    #[ORM\OneToMany(targetEntity: Winner::class, mappedBy: 'table', cascade: ['persist'], orphanRemoval: true)]
    private Collection $winners;

    #[ORM\ManyToOne(targetEntity: TableSetting::class, inversedBy: 'tables')]
    #[ORM\JoinColumn(name: 'setting_id', referencedColumnName: 'id')]
    private ?TableSetting $setting;

    public function __construct()
    {
        $this->tableUsers        = new ArrayCollection();
        $this->tableUserInvoices = new ArrayCollection();
        $this->chats             = new ArrayCollection();
        $this->tableHistory      = new ArrayCollection();
        $this->tournamentUsers   = new ArrayCollection();
        $this->spectators        = new ArrayCollection();
        $this->banks             = new ArrayCollection();
        $this->winners           = new ArrayCollection();
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

    public function getMaxBet(): ?float
    {
        return $this->maxBet;
    }

    public function setMaxBet(?float $maxBet): static
    {
        $this->maxBet = $maxBet;

        return $this;
    }

    public function getRound(): Round
    {
        return $this->round;
    }

    public function setRound(Round $round): static
    {
        $this->round = $round;

        return $this;
    }

    public function getFormattedRound(): ?string
    {
        return $this->round ? $this->round->value : null;
    }

    public function setFormattedStatus($status): static
    {
        $this->round = Round::tryFrom($status);

        return $this;
    }

    public function isMainRound(): bool
    {
        return in_array(
            $this->round->value,
            [
                Round::PreFlop->value,
                Round::Flop->value,
                Round::Turn->value,
                Round::River->value
            ],
            true
        );
    }

    public function getDealerPlace(): ?int
    {
        return $this->dealerPlace;
    }

    public function setDealerPlace(?int $dealerPlace): static
    {
        $this->dealerPlace = $dealerPlace;

        return $this;
    }

    public function getSmallBlindPlace(): ?int
    {
        return $this->smallBlindPlace;
    }

    public function setSmallBlindPlace(?int $smallBlindPlace): static
    {
        $this->smallBlindPlace = $smallBlindPlace;

        return $this;
    }

    public function getBigBlindPlace(): ?int
    {
        return $this->bigBlindPlace;
    }

    public function setBigBlindPlace(?int $bigBlindPlace): static
    {
        $this->bigBlindPlace = $bigBlindPlace;

        return $this;
    }

    public function getTurnPlace(): ?int
    {
        return $this->turnPlace;
    }

    public function setTurnPlace(?int $turnPlace): static
    {
        $this->turnPlace = $turnPlace;

        return $this;
    }

    public function getTableUsers(): Collection
    {
        return $this->tableUsers;
    }

    public function setTableUsers(Collection $tableUsers): static
    {
        $this->tableUsers = $tableUsers;

        return $this;
    }

    public function getTableUserInvoices(): Collection
    {
        return $this->tableUserInvoices;
    }

    public function setTableUserInvoices(Collection $tableUserInvoices): static
    {
        $this->tableUserInvoices = $tableUserInvoices;

        return $this;
    }

    public function getLastWordPlace(): ?int
    {
        return $this->lastWordPlace;
    }

    public function setLastWordPlace(?int $lastWordPlace): static
    {
        $this->lastWordPlace = $lastWordPlace;

        return $this;
    }

    public function getCards(?bool $toArray = false): array
    {
        if (!$this->cards) {
            return [];
        }

        if ($toArray) {
            return $this->cards;
        }

        $cards = [];
        foreach ($this->cards as $card) {
            $cards[] = (new Card())->fromArray($card);
        }

        return $cards;
    }

    public function setCards(Card ...$cards): static
    {
        $this->cards = [];

        foreach ($cards as $card) {
            $this->cards[] = $card->toArray();
        }

        return $this;
    }

    public function removeCards(): static
    {
        $this->cards = [];

        return $this;
    }

    public function addCard(Card $card): static
    {
        $this->cards[] = $card->toArray();

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

    public function getRoundExpirationTime(): ?int
    {
        return $this->roundExpirationTime;
    }

    public function setRoundExpirationTime(?int $roundExpirationTime): static
    {
        $this->roundExpirationTime = $roundExpirationTime;

        return $this;
    }

    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): static
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setTable($this);
        }

        return $this;
    }

    public function getTableHistory(): Collection
    {
        return $this->tableHistory;
    }

    public function addTableHistory(TableHistory $tableHistory): static
    {
        if (!$this->tableHistory->contains($tableHistory)) {
            $this->tableHistory->add($tableHistory);
            $tableHistory->setTable($this);
        }

        return $this;
    }

    public function removeTableHistory(TableHistory $tableHistory): static
    {
        if ($this->tableHistory->removeElement($tableHistory)) {
            if ($tableHistory->getTable() === $this) {
                $tableHistory->setTable(null);
            }
        }

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getFreePlaces(): array
    {
        $occupiedPlaces = $this->tableUsers->map(fn(TableUser $tableUser) => $tableUser->getPlace())->toArray();

        $freePlaces = [];
        for ($i = 1; $i <= $this->setting->getCountPlayers(); $i++) {
            if (!in_array($i, $occupiedPlaces)) {
                $freePlaces[] = $i;
            }
        }

        return $freePlaces;
    }

    public function getRakeStatus(): ?bool
    {
        return $this->rakeStatus;
    }

    public function setRakeStatus(?bool $rakeStatus): static
    {
        $this->rakeStatus = $rakeStatus;

        return $this;
    }

    public function getSetting(): ?TableSetting
    {
        return $this->setting;
    }

    public function setSetting(TableSetting $tableSetting): static
    {
        $this->setting = $tableSetting;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getReconnectTime(): ?int
    {
        return $this->reconnectTime;
    }

    public function setReconnectTime(?int $reconnectTime): static
    {
        $this->reconnectTime = $reconnectTime;

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

    public function __toString(): string
    {
        return $this->name;
    }
}
