<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\{BetType, CardSuit, CardType, TableUserStatus};
use App\Repository\TableUserRepository;
use App\ValueObject\{Card, Combination , TableUserTimeBank};
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

#[ORM\Entity(repositoryClass: TableUserRepository::class)]
#[ORM\Table(name: '`table_user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['table', 'place'],
    message: 'This place is already in use on that table.',
    errorPath: 'place',
)]
#[UniqueEntity(
    fields: ['table', 'user'],
    message: 'This user is already behind this table.',
    errorPath: 'user',
)]
class TableUser
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tableUsers')]
    #[ORM\JoinColumn(name: 'table_id', referencedColumnName: 'id')]
    private Table $table;

    #[ORM\ManyToOne(inversedBy: 'tableUsers')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\Column(name: "place", type: Types::INTEGER, options: ["default" => 0])]
    private int $place = 0;

    #[ORM\Column(name: "stack", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private float $stack = 20;

    #[ORM\Column(name: "status", type: Types::STRING, enumType: TableUserStatus::class, length: 255, options: ["default" => TableUserStatus::Pending->value])]
    private TableUserStatus $status = TableUserStatus::Pending;

    #[ORM\Column(name: "bet", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private float $bet = 0;

    #[ORM\Column(name: "bet_sum", type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private float $betSum = 0;

    #[ORM\Column(name: "bet_type", type: Types::STRING, enumType: BetType::class, length: 255, nullable: true)]
    private ?BetType $betType = null;

    #[ORM\Column(name: "bet_expiration_time", type: Types::INTEGER, options: ["default" => 0])]
    private int $betExpirationTime = 0;

    #[ORM\Column(name: "cards", type: Types::JSON, nullable: true)]
    private ?array $cards = [];

    #[ORM\Column(name: "leaver", type: Types::BOOLEAN, nullable: true, options: ["default" => false])]
    private bool $leaver = false;

    #[ORM\Column(name: "seat_out", type: Types::INTEGER, nullable: true)]
    private ?int $seatOut = 0;

    #[ORM\Column(name: "count_buy_in", type: Types::INTEGER, nullable: true)]
    private ?int $countByuIn = 0;

    #[ORM\Column(name: "time_bank", type: Types::JSON, nullable: true)]
    private ?array $timeBank = [];

    #[ORM\OneToMany(targetEntity: Winner::class, mappedBy: 'tableUser', cascade: ['persist'], orphanRemoval: true)]
    private Collection $winners;

    private ?Combination $combination = null;

    public function getCombination(): ?Combination
    {
        return $this->combination;
    }

    public function setCombination(?Combination $combination): static
    {
        $this->combination = $combination;

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

    public function getPlace(): int
    {
        return $this->place;
    }

    public function setPlace(int $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getStack(): float
    {
        return $this->stack;
    }

    public function setStack(float $stack): static
    {
        $this->stack = $stack;

        return $this;
    }

    public function getStatus(): TableUserStatus
    {
        return $this->status;
    }

    public function getFormattedStatus(): ?string
    {
        return $this->status ? $this->status->value : null;
    }

    public function setStatus(TableUserStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBet(): float
    {
        return $this->bet;
    }

    public function setBet(float $bet): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getBetType(): ?BetType
    {
        return $this->betType;
    }

    public function getFormattedBetType(): ?string
    {
        return $this->betType ? $this->betType->value : null;
    }

    public function setBetType(?BetType $betType): static
    {
        $this->betType = $betType;

        return $this;
    }

    public function getBetExpirationTime(): int
    {
        return $this->betExpirationTime;
    }

    public function getFormattedBetExpirationTime(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->betExpirationTime);
    }

    public function setBetExpirationTime(int $betExpirationTime): static
    {
        $this->betExpirationTime = $betExpirationTime;

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
            $cards[] = (new Card())
                ->setValue($card['value'])
                ->setName($card['name'])
                ->setType(CardType::tryFrom($card['type']))
                ->setSuit(CardSuit::tryFrom($card['suit']))
                ->setView($card['view']);
        }

        return $this->sortCards(...$cards);
    }

    public function setCards(Card ...$cards): static
    {
        $this->cards = [];

        foreach ($cards as $card) {
            $this->cards[] = [
                'name'  => $card->getName(),
                'value' => $card->getValue(),
                'suit'  => $card->getSuit()->value,
                'view'  => $card->getView(),
                'type'  => $card->getType()->value,
            ];
        }

        return $this;
    }

    public function sortCards(Card ...$cards): ?array
    {
        usort(
            $cards,
            fn($prev, $next) => $next->getValue() <=> $prev->getValue()
        );

        $groupedCardsBySuits = [];
        foreach ($cards as $card) {
            $groupedCardsBySuits[$card->getSuit()->value][] = $card;
        }

        $sortedCards = [];
        foreach (CardSuit::cases() as $cardSuitCase) {
            if (!array_key_exists($cardSuitCase->value, $groupedCardsBySuits)) {
                continue;
            }

            $sortedCards = [...$sortedCards, ...$groupedCardsBySuits[$cardSuitCase->value]];
        }

        return $sortedCards;
    }

    public function removeCards(): static
    {
        $this->cards = [];

        return $this;
    }

    public function addCard(Card $card): static
    {
        $this->cards[] = [
            'name'  => $card->getName(),
            'value' => $card->getValue(),
            'suit'  => $card->getSuit()->value,
            'type'  => $card->getType()->value,
        ];

        return $this;
    }

    public function getBetSum(): float
    {
        return $this->betSum;
    }

    public function setBetSum(float $betSum): static
    {
        $this->betSum = $betSum;

        return $this;
    }

    public function getLeaver(): bool
    {
        return $this->leaver;
    }

    public function setLeaver(bool $leaver): static
    {
        $this->leaver = $leaver;

        return $this;
    }

    public function getSeatOut(): ?int
    {
        return $this->seatOut;
    }

    public function setSeatOut(?int $seatOut): static
    {
        $this->seatOut = $seatOut;

        return $this;
    }

    public function getCountByuIn(): ?int
    {
        return $this->countByuIn;
    }

    public function setCountByuIn(?int $countByuIn): void
    {
        $this->countByuIn = $countByuIn;
    }

    public function getTimeBank(): TableUserTimeBank
    {
        $timeBank = new TableUserTimeBank();

        if (!$this->timeBank) {
            return $timeBank;
        }

        return !$this->timeBank ? $timeBank : $timeBank->fromArray($this->timeBank);
    }

    public function setTimeBank(TableUserTimeBank $timeBank): static
    {
        $this->timeBank = $timeBank->toArray();

        return $this;
    }

    #[ORM\PreRemove]
    public function setRank(PreRemoveEventArgs $event)
    {
        // Update table state cache
        // $cache = new ApcuAdapter();
        // $cacheKey  = 'table_state_' . $this->table->getId();
        // $cacheItem = $cache->getItem($cacheKey);

        // $cacheData = $cacheItem->isHit() ? $cacheItem->get() : [];

        // if (array_key_exists('players', $cacheData) && array_key_exists($this->place, $cacheData['players'])) {
        //     unset($cacheData['players'][$this->place]);
        // }

        // $cacheItem->set($cacheData);
        // $cacheItem->expiresAfter(3600); // 1 час
        // $cache->save($cacheItem);

        // Tournament
        if (!$this->table->getTournament()) {
            return;
        }

        $tournamentUsers = $this->table->getTournament()->getTournamentUsers()->filter(
            fn(TournamentUser $tournamentUser) => $tournamentUser->getUser()->getId() === $this->user->getId()
        );

        $ranks = $this->table->getTournament()->getTournamentUsers()
            ->filter(fn(TournamentUser $tournamentUser) => $tournamentUser->getRank())
            ->map(fn(TournamentUser $tournamentUser) => $tournamentUser->getRank())
            ->getValues();

        $rank = count($ranks) > 0 ? min($ranks) - 1 : $this->table->getTournament()->getTournamentUsers()->count();

        if ($tournamentUsers->count() < 1) {
            return;
        }

        $tournamentUser = $tournamentUsers->first()->setRank($rank);
        $om             = $event->getObjectManager();
        $om->persist($tournamentUser);
    }
}
