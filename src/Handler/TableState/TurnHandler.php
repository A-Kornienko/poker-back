<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Entity\{Table, TableUser};
use App\Enum\{BetType, Round, TableUserStatus};
use App\Exception\ResponseException;
use App\Helper\ErrorCodeHelper;
use App\Repository\TableUserRepository;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TurnHandler
{
    public const BET_EXPIRATION_TIME = 1;

    public function __construct(
        protected TableUserRepository $tableUserRepository,
        protected EntityManagerInterface $entityManager,
        protected TranslatorInterface $translator,
        protected PlayerService $playerService
    ) {
    }

    public function validateTurn(Table $table, TableUser $tableUser): ResponseException|bool
    {
        return match (true) {
            $tableUser->getBetExpirationTime() < time()                        => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TIME_OVER),
            $table->getTurnPlace() !== $tableUser->getPlace()                  => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::PLAYER_WRONG_TURN),
            $tableUser->getStatus()?->value !== TableUserStatus::Active->value => ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::USER_INACTIVE),
            default                                                            => true
        };
    }

    /**
     * Устанавливаем диллера.
     */
    public function setDealer(Table $table, array $activePlayersSortedByPlace): Table
    {
        /** @var TableUser $tableUser */
        // Список мест занятых за столом.
        $places = array_keys($activePlayersSortedByPlace);
        // Номер первого занятого места.
        $firstPlace = array_key_first($activePlayersSortedByPlace);
        // Тут мы узнаем какой индекс у места диллера.
        $dealerPlaceIndex = (int) array_search($table->getDealerPlace(), $places);
        // Определяем индекс для нового места диллера.
        $newDealerIndex = $dealerPlaceIndex + 1;
        // Определяем новое место диллеру, если индекс последний, то новый диллер становится на 1 место.
        $newDealerPlace = array_key_exists($newDealerIndex, $places) ? $places[$newDealerIndex] : $firstPlace;

        return $table->setDealerPlace($newDealerPlace);
    }

    /**
     * Определяем какое место является первым в очереди на ход.
     * По правилам первым должен ходить игрок после диллера.
     * Исключения:
     * 1. На префлопе первое слово за игроком после большого блайнда.
     * 2. Когда за столом 2 игрока первый ход за диллером.
     */
    public function setFirstTurn(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        return $table->getRound()->value === Round::PreFlop->value
            ? $this->setFirstTurnFirstRound($table, $activeTableUsersSortedByPlace)
            : $this->setFirstTurnDefault($table, $activeTableUsersSortedByPlace);
    }

    // Двигаем право хода к следующему автивному месту.
    public function changeTurnPlace(Table $table): void
    {
        $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);
        $activePlayersSortedByPlace = $this->playerService->excludeSilentPlayers($activePlayersSortedByPlace);
        $table                      = $this->updateTurnPlace($table, $activePlayersSortedByPlace, $table->getTurnPlace());

        $this->entityManager->persist($table);
        $this->entityManager->flush();
    }

    // Меняем право на последнее слово.
    public function changeLastWordPlace(Table $table, TableUser $currentActiveTableUser): Table
    {
        $activePlayersSortedByPlace = $this->tableUserRepository->getPlayersSortedByPlace($table);

        $table = $this->updateLastWordPlace($table, $activePlayersSortedByPlace);
        $this->entityManager->persist($table);
        $this->entityManager->flush();

        return $table;
    }

    /**
     * По правилам в 1 раунде начинает круг первый игрок после большого блайнда
     */
    protected function setFirstTurnFirstRound(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        // Получаем список всех активных мест.
        $activePlaces = array_keys($activeTableUsersSortedByPlace);
        // Определяем первое активное место.
        $firstPlaceNumber = array_key_first($activeTableUsersSortedByPlace);
        // Получаем место на котором находится большой блайнд.
        $lastWordPlaceIndex = array_search($table->getBigBlindPlace(), $activePlaces);
        // Определяем индекс для первого хода.
        $firstTurnIndex = $lastWordPlaceIndex + 1;
        // Проверяем не является ли большой блайнд последним местом.
        $isBigBlindNotLastPlace = $lastWordPlaceIndex !== false && array_key_exists($firstTurnIndex, $activePlaces);

        if (
            count($this->playerService->excludeSilentPlayers($activeTableUsersSortedByPlace)) > 0
            && $activeTableUsersSortedByPlace[$activePlaces[$lastWordPlaceIndex]]->getBetType()?->value === BetType::AllIn->value
        ) {
            $lastWordPlaceIndex = $this->calculateLastWordIndex($activeTableUsersSortedByPlace, $lastWordPlaceIndex);
        }

        // Если большой блайн находится не на последнем месте устанавливаем первый ход следующему месту после него
        if ($isBigBlindNotLastPlace) {
            $turnPlace = $activePlaces[$firstTurnIndex];
            if ($activeTableUsersSortedByPlace[$turnPlace]->getSeatOut()) {
                $betExpirationTime = time() + 10;
            } else {
                $betExpirationTime = time() + ((int)$table->getSetting()->getTurnTime() ?? static::BET_EXPIRATION_TIME);
            }
            $activeTableUsersSortedByPlace[$turnPlace]->setBetExpirationTime($betExpirationTime);

            $this->entityManager->persist($activeTableUsersSortedByPlace[$turnPlace]);
            $this->entityManager->flush();

            return $table->setTurnPlace($activePlaces[$firstTurnIndex])
                ->setLastWordPlace($activePlaces[$lastWordPlaceIndex]);
        }

        if ($activeTableUsersSortedByPlace[$firstPlaceNumber]->getSeatOut()) {
            $betExpirationTime = time() + 10;
        } else {
            $betExpirationTime = time() + ((int)$table->getSetting()->getTurnTime() ?? static::BET_EXPIRATION_TIME);
        }
        // Если большой блайн находится на последнем месте устанавливаем первый ход, первому месту в списке.
        $activeTableUsersSortedByPlace[$firstPlaceNumber]
            ->setBetExpirationTime($betExpirationTime);
        $table->setTurnPlace($firstPlaceNumber)
            ->setLastWordPlace($activePlaces[$lastWordPlaceIndex]);

        $this->entityManager->persist($activeTableUsersSortedByPlace[$firstPlaceNumber]);
        $this->entityManager->flush();

        return $table;
    }

    /**
     * По правилам первый ход имее игрок первый после диллера.
     * Так как игроки могут прибывать в состоянии неактивности в том числе и диллер,
     * нам нужно взять весь список игроков за столом и найти первое активное после диллера
     */
    protected function setFirstTurnDefault(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        // Определяем место за которым первый ход
        $activeTableUsersSortedByPlace = $this->playerService->excludeSilentPlayers($activeTableUsersSortedByPlace);
        $table                         = $this->updateTurnPlace($table, $activeTableUsersSortedByPlace, $table->getDealerPlace());

        // Обновляем право на последнее слово.
        return $this->updateLastWordPlace($table, $activeTableUsersSortedByPlace);
    }

    /**
     * Двигаем право на ход, следуюшему активному игроку.
     */
    protected function updateTurnPlace(
        Table $table,
        array $activeTableUsersSortedByPlace,
        int $currentPlace
    ): Table {
        // Получаем список всех игроков за столом отсортированый по возрастанию мест.
        $sortedAllTableUsers = $this->sortTableUsersByPlaceAsc($table->getTableUsers()->toArray());
        // Получаем список всех мест
        $places = array_keys($sortedAllTableUsers);
        // Получаем индекс места следующего после текущего.
        $nextTurnPlaceIndex = (int) array_search($currentPlace, $places) + 1;
        // Определяем количество мест в списке после текущего
        $countPlaceAfterCurrentTurn = count($sortedAllTableUsers) - $nextTurnPlaceIndex;

        // Если текущее место не последнее место за столом, тогда вырезаем все места после текущего и добавляем их в начало списка.
        // Таким образом мы понимает что текущее место является последним в списке, а значит следующий ход у первогоа активного места.
        if ($countPlaceAfterCurrentTurn > 0) {
            $tableUsersAfterCurrentTurn          = array_slice($sortedAllTableUsers, -$countPlaceAfterCurrentTurn);
            $tableUsersBeforeCurrentTurnIncluded = array_slice($sortedAllTableUsers, 0, $nextTurnPlaceIndex);
            // Sort users from dealer
            $sortedAllTableUsers = array_merge($tableUsersAfterCurrentTurn, $tableUsersBeforeCurrentTurnIncluded);
        }

        //Находим первого активного юзера за столом после текущего и присваиваем ему право первого хода.
        /** @var TableUser $tableUser */
        foreach ($sortedAllTableUsers as $tableUser) {
            if (array_key_exists($tableUser->getPlace(), $activeTableUsersSortedByPlace)) {
                if ($tableUser->getSeatOut()) {
                    $tableUser->setBetExpirationTime(time() + 10);
                } else {
                    $tableUser->setBetExpirationTime(time() + ((int)$table->getSetting()->getTurnTime() ?? static::BET_EXPIRATION_TIME));
                }
                $table->setTurnPlace($tableUser->getPlace());

                $this->entityManager->persist($tableUser);
                break;
            }
        }

        $this->entityManager->flush();

        return $table;
    }

    /**
     * В начале игры право последнего хода, получает игрок который сидит перед игроком у которого право первого хода.
     *
     * Когда игрок походил и сделал повышающую ставку,
     * мы передаем право последнего хода, игроку который находится перед ним.
     *
     * В других случаях право последнего хода не меняется.
     */
    protected function updateLastWordPlace(Table $table, array $activeTableUsersSortedByPlace): Table
    {
        $lastWordPlace = 0;
        foreach ($activeTableUsersSortedByPlace as $activeTableUserSortedByPlace) {
            if ($activeTableUserSortedByPlace->getPlace() === $table->getTurnPlace()) {
                break;
            }

            if (
                $activeTableUserSortedByPlace->getBetType()
                && $activeTableUserSortedByPlace->getBetType()?->value !== BetType::AllIn->value
                && $activeTableUserSortedByPlace->getBetType()?->value !== BetType::Fold->value
            ) {
                $lastWordPlace = $activeTableUserSortedByPlace->getPlace();
            }
        }

        $activeTableUsersSortedByPlace = $this->playerService->excludeSilentPlayers($activeTableUsersSortedByPlace);
        if (!$lastWordPlace) {
            $lastWordPlace = array_key_last($activeTableUsersSortedByPlace);
        }
        $table->setLastWordPlace($lastWordPlace);

        return $table;
    }

    protected function sortTableUsersByPlaceAsc(array $tableUsers): array
    {
        usort(
            $tableUsers,
            fn($prev, $next) => $prev->getPlace() <=> $next->getPlace()
        );

        $sortedTableUsers = [];
        foreach ($tableUsers as $tableUser) {
            $sortedTableUsers[$tableUser->getPlace()] = $tableUser;
        }

        return $sortedTableUsers;
    }

    private function calculateLastWordIndex(array $activeTableUsersSortedByPlace, int $currentPlaceIndex): int
    {
        $activePlaces = array_keys($activeTableUsersSortedByPlace);

        // Используем do-while чтобы цикл выполнился хотя бы один раз
        do {
            // Найти индекс предыдущего места за столом
            $currentPlaceIndex = $currentPlaceIndex === 0 ? count($activePlaces) - 1 : $currentPlaceIndex - 1;

            // Получить место
            $currentPlace = $activePlaces[$currentPlaceIndex];

            // Получить тип ставки текущего игрока
            $currentBetType = $activeTableUsersSortedByPlace[$currentPlace]->getBetType()?->value;
        } while ($currentBetType === BetType::AllIn->value);

        return $currentPlaceIndex;
    }
}
