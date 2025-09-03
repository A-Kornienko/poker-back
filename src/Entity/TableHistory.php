<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\TableHistoryRepository;
use App\ValueObject\Card;
use App\ValueObject\TableHistory\BlindsTableHistory;
use App\ValueObject\TableHistory\PlayerTableHistory;
use App\ValueObject\TableHistory\PotTableHistory;
use App\ValueObject\TableHistory\RoundActionTableHistory;
use App\ValueObject\TableHistory\WinnerTableHistory;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableHistoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TableHistory
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne(inversedBy: 'tableHistory')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\Column(name: "session", type: Types::STRING, nullable: false)]
    private ?string $session = null;

    #[ORM\Column(name: "players", type: Types::JSON, nullable: false)]
    private array $players;

    #[ORM\Column(name: "blinds", type: Types::JSON, nullable: false)]
    private array $blinds;

    #[ORM\Column(name: "dealer", type: Types::INTEGER, nullable: false)]
    private int $dealer;

    #[ORM\Column(name: "cards", type: Types::JSON, nullable: true)]
    private ?array $cards = [];

    #[ORM\Column(name: "preflop", type: Types::JSON, nullable: true)]
    private ?array $preflop = [];

    #[ORM\Column(name: "flop", type: Types::JSON, nullable: true)]
    private ?array $flop = [];

    #[ORM\Column(name: "turn", type: Types::JSON, nullable: true)]
    private ?array $turn = [];

    #[ORM\Column(name: "river", type: Types::JSON, nullable: true)]
    private ?array $river = [];

    #[ORM\Column(name: "pot", type: Types::JSON, nullable: true)]
    private ?array $pot = [];

    #[ORM\Column(name: "winners", type: Types::JSON, nullable: true)]
    private ?array $winners = [];

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

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getPlayers(): array
    {
        $players = [];
        foreach ($this->players as $player) {
            $players[$player['place']] = (new PlayerTableHistory())->fromArray($player);
        }

        return $players;
    }

    public function setPlayers(array $players): static
    {
        $this->players = [];

        foreach ($players as $player) {
            $this->players[] = $player->toArray();
        }

        return $this;
    }

    public function getBlinds(): BlindsTableHistory
    {
        return (new BlindsTableHistory())->fromArray($this->blinds);
    }

    public function setBlinds(BlindsTableHistory $blinds): static
    {
        $this->blinds = $blinds->toArray();

        return $this;
    }

    public function getDealer(): int
    {
        return $this->dealer;
    }

    public function setDealer(int $dealer): static
    {
        $this->dealer = $dealer;

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

    public function getPreflop(): array
    {
        $preflop = [];

        foreach ($this->preflop as $roundAction) {
            $preflop[] = (new RoundActionTableHistory())->fromArray($roundAction);
        }

        return $preflop;
    }

    public function setPreflop(RoundActionTableHistory ...$preflop): static
    {
        $this->preflop = [];

        foreach ($preflop as $roundAction) {
            $this->preflop[] = $roundAction->toArray();
        }

        return $this;
    }

    public function addPreflop(RoundActionTableHistory $roundAction): static
    {
        $this->preflop[] = $roundAction->toArray();

        return $this;
    }

    public function getFlop(): array
    {
        $flop = [];

        foreach ($this->flop as $roundAction) {
            $flop[] = (new RoundActionTableHistory())->fromArray($roundAction);
        }

        return $flop;
    }

    public function setFlop(RoundActionTableHistory ...$flop): static
    {
        $this->flop = [];
        foreach ($flop as $roundAction) {
            $this->flop[] = $roundAction->toArray();
        }

        return $this;
    }

    public function addFlop(RoundActionTableHistory $roundAction): static
    {
        $this->flop[] = $roundAction->toArray();

        return $this;
    }

    public function getTurn(): array
    {
        $turn = [];
        foreach ($this->turn as $roundAction) {
            $turn[] = (new RoundActionTableHistory())->fromArray($roundAction);
        }

        return $turn;
    }

    public function setTurn(RoundActionTableHistory ...$turn): static
    {
        $this->turn = [];
        foreach ($turn as $roundAction) {
            $this->turn[] = $roundAction->toArray();
        }

        return $this;
    }

    public function addTurn(RoundActionTableHistory $roundAction): static
    {
        $this->turn[] = $roundAction->toArray();

        return $this;
    }

    public function getRiver(): array
    {
        $river = [];
        foreach ($this->river as $roundAction) {
            $river[] = (new RoundActionTableHistory())->fromArray($roundAction);
        }

        return $river;
    }

    public function setRiver(RoundActionTableHistory ...$river): static
    {
        $this->river = [];
        foreach ($river as $roundAction) {
            $this->river[] = $roundAction->toArray();
        }

        return $this;
    }

    public function addRiver(RoundActionTableHistory $roundAction): static
    {
        $this->river[] = $roundAction->toArray();

        return $this;
    }

    public function getPot(): PotTableHistory
    {
        return (new PotTableHistory())->fromArray($this->pot);
    }

    public function setPot(PotTableHistory $pot): static
    {
        $this->pot = $pot->toArray();

        return $this;
    }

    public function getWinners(): array
    {
        $winners = [];

        foreach ($this->winners as $winner) {
            $winners[] = (new WinnerTableHistory())->fromArray($winner);
        }

        return $winners;
    }

    public function setWinners(WinnerTableHistory ...$winners): static
    {
        $this->winners = [];

        foreach ($winners as $winner) {
            $this->winners[] = $winner->toArray();
        }

        return $this;
    }

    public function addWinner(WinnerTableHistory $winner): static
    {
        $this->winners[] = $winner->toArray();

        return $this;
    }
}
