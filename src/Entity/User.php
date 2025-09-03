<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use inc\User as MainUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "external_id", type: Types::INTEGER, options: ["default" => 0])]
    private int $externalId = 0;

    #[ORM\Column(name: "login", type: Types::STRING, length: 70)]
    private string $login;

    #[ORM\Column(name: "email", type: Types::STRING, length: 70)]
    private string $email;

    #[ORM\Column(name: "password", type: Types::STRING, length: 70)]
    private string $password;

    #[ORM\Column(name: "avatar", type: Types::STRING, length: 255, options: ["default" => ''])]
    private string $avatar = '';

    #[ORM\Column(name: "last_login", type: Types::INTEGER, options: ["default" => 0])]
    private int $lastLogin = 0;

    #[ORM\Column(name: 'role', type: Types::STRING, nullable: false, enumType: UserRole::class, options: ["default" => UserRole::Player->value])]
    private UserRole $role = UserRole::Player;

    #[ORM\Column(name: "language", type: Types::STRING, length: 255, options: ["default" => 'ru'])]
    private string $language = 'ru';

    /**
     * @var Collection<int, TableUser>
     */
    #[ORM\OneToMany(targetEntity: TableUser::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tableUsers;

    /**
     * @var Collection<int, TableUserInvoice>
     */
    #[ORM\OneToMany(targetEntity: TableUserInvoice::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tableUserInvoices;

    #[ORM\ManyToMany(targetEntity: Bank::class, mappedBy: 'users', cascade: ['persist'], orphanRemoval: true)]
    private ?Collection $banks = null;

    /**
     * @var Collection<int, Chat>
     */
    #[ORM\OneToMany(targetEntity: Chat::class, mappedBy: 'user')]
    private Collection $chats;

    #[ORM\OneToMany(targetEntity: TournamentUser::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tournamentUsers;

    #[ORM\OneToMany(targetEntity: TournamentPrize::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $prizes;

    #[ORM\OneToMany(targetEntity: Winner::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $winners;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?PlayerSetting $playerSetting = null;

    public function __construct()
    {
        $this->tableUsers        = new ArrayCollection();
        $this->tableUserInvoices = new ArrayCollection();
        $this->banks             = new ArrayCollection();
        $this->chats             = new ArrayCollection();
        $this->tournamentUsers   = new ArrayCollection();
        $this->prizes            = new ArrayCollection();
        $this->winners           = new ArrayCollection();
        $this->notifications     = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getLastLogin(): int
    {
        return $this->lastLogin;
    }

    public function getFormattedLastLogin(): \DateTimeInterface
    {
        return (new \DateTime())->setTimestamp($this->lastLogin);
    }

    public function setLastLogin(int $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getTableUsers(): Collection
    {
        return $this->tableUsers;
    }

    public function addTableUser(TableUser $tableUser): static
    {
        if (!$this->tableUsers->contains($tableUser)) {
            $this->tableUsers->add($tableUser);
            $tableUser->setUser($this);
        }

        return $this;
    }

    public function getTableUserInvoices(): Collection
    {
        return $this->tableUserInvoices;
    }

    public function addTableUserInvoice(TableUserInvoice $tableUserInvoice): static
    {
        if (!$this->tableUserInvoices->contains($tableUserInvoice)) {
            $this->tableUserInvoices->add($tableUserInvoice);
            $tableUserInvoice->setUser($this);
        }

        return $this;
    }

    // Методы, требуемые интерфейсом UserInterface
    public function getRoles(): array
    {
        return !$this->role ? [UserRole::Player->value] : [$this->role->value];
    }

    public function getFormattedRole(): ?string
    {
        return $this->role ? $this->role->value : null;
    }

    public function eraseCredentials(): void
    {
        // Очистка временных данных, например plainPassword
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getBanks(): ?Collection
    {
        return $this->banks;
    }

    public function setBanks(?ArrayCollection $banks): static
    {
        $this->banks = $banks;

        return $this;
    }

    public function getChats(): ?Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): static
    {
        if (!$this->chats->contains($chat)) {
            $this->chats->add($chat);
            $chat->setUser($this);
        }

        return $this;
    }

    public function getMainUser(): ?MainUser
    {
        $main_user = MainUser::findOneBy(['id' => $this->externalId]);

        // if (!$main_user) {
        //     //throw new Exception('Can`t get main user');
        // }

        return $main_user;
    }

    public function getTournaments(): Collection
    {
        return $this->tournamentUsers->map(fn(TournamentUser $tu) => $tu->getTournament());
    }

    public function getTables(): ArrayCollection
    {
        $tables = new ArrayCollection();
        foreach ($this->tableUsers as $tableUser) {
            $tables->add($tableUser->getTable());
        }

        return $tables;
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

    public function getNotifications(): ?Collection
    {
        return $this->notifications;
    }

    public function setNotifications(Collection $notifications): static
    {
        $this->notifications = $notifications;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getPlayerSetting(): ?PlayerSetting
    {
        return $this->playerSetting;
    }

    public function setPlayerSetting(PlayerSetting $playerSetting): static
    {
        if ($playerSetting->getUser() !== $this) {
            $playerSetting->setUser($this);
        }

        $this->playerSetting = $playerSetting;

        return $this;
    }

    public function __toString()
    {
        return $this->login;
    }
}
