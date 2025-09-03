<?php

declare(strict_types=1);

namespace App\Handler\Tournaments;

use api\exchange\Currency;
use App\Entity\Tournament;
use App\Entity\TournamentUser;
use App\Handler\AbstractHandler;
use App\Repository\TableUserRepository;
use App\Repository\TournamentPrizeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResolvePrizesTournamentHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected EventDispatcherInterface $dispatcher,
        protected EntityManagerInterface $entityManager,
        protected TournamentPrizeRepository $tournamentPrizeRepository,
        protected TableUserRepository $tableUserRepository,
        protected FinishTournamentHandler $finishTournamentHandler,
    ) {
        parent::__construct($security, $translator);
    }

    protected function resolveUnclaimed(Collection $tournamentUsers, array $tournamentPrizes): void
    {
        //возвращает призы без победителя
        foreach ($tournamentPrizes as $key => $tournamentPrize) {
            if ($tournamentPrize->getWinner()) {
                continue;
            }

            foreach ($tournamentUsers->toArray() as $tournamentUser) {
                if ($tournamentUser->getRank() === $key + 1) {
                    $tournamentPrize->setWinner($tournamentUser->getUser());
                    $mainUser     = $tournamentUser->getUser()->getMainUser();
                    $convertedSum = Currency::converter($tournamentPrize->getSum(), 'USD', $mainUser->getUserInfo()['currency']);
                    $mainUser->changeBalance($convertedSum);

                    $this->entityManager->persist($tournamentPrize);
                }
            }
        }

        $this->entityManager->flush();
    }

    protected function resolve(Tournament $tournament, Collection $tournamentUsers, array $tournamentPrizes): void
    {
        $tables = $tournament->getTables()->filter(fn($table) => $table->getTableUsers()->count() > 0)
            ->map(fn($table) => $table->getId())->getValues();
        $tableUsers = $this->tableUserRepository->findBy([
            'table' => $tables
        ]);

        if (count($tableUsers) === 1) {
            $tableUserWinner  = current($tableUsers);
            $tournamentWinner = $tournamentUsers->filter(
                fn(TournamentUser $tournamentUser) => $tournamentUser->getUser()->getId() === $tableUserWinner->getUser(
                )->getId()
            )->first();

            $tournamentPrize = reset($tournamentPrizes);

            if (!$tournamentPrize->getWinner()) {
                $tournamentPrize = $tournamentPrize->setWinner($tournamentWinner->getUser());
                $mainUser        = $tournamentWinner->getUser()->getMainUser();
                $convertedSum    = Currency::converter(
                    $tournamentPrize->getSum(),
                    'USD',
                    $mainUser->getUserInfo()['currency']
                );
                $mainUser->changeBalance(
                    $convertedSum,
                    'Poker: You won in tournament ' . $tournament->getName(
                    ) . ', your rank is ' . $tournamentWinner->getRank()
                );
            }

            $this->entityManager->persist($tournamentPrize);
            $this->entityManager->remove($tableUserWinner);
            $this->entityManager->flush();
        }
    }

    public function __invoke(Tournament $tournament): void
    {
        $tournamentPrizes = $this->tournamentPrizeRepository->findBy(
            criteria: [
                'tournament' => $tournament,
            ],
            orderBy: ['sum' => 'DESC']
        );

        $tournamentUsers = $tournament->getTournamentUsers()->filter(
            fn(TournamentUser $tournamentUser) => $tournamentUser->getRank() <= count($tournamentPrizes)
        );

        $this->resolveUnclaimed($tournamentUsers, $tournamentPrizes);
        $this->resolve($tournament, $tournamentUsers, $tournamentPrizes);

        ($this->finishTournamentHandler)($tournament);
    }
}
