<?php

declare(strict_types=1);

namespace App\Handler\Balance\Rebuy;

use App\Entity\{Table, User};

class ReBuyTournamentHandler extends AbstractRebuyBalanceHandler
{
    public function __invoke(User $user, Table $table): void
    {
        $chips = $table->getTournament()->getSetting()->getBuyInSettings()->getChips();

        $player = $this->getTableUser($user, $table);

        $this->validateTournamentBuyIn($player, $chips);

        $this->defaultBuyIn($table, $user, $chips);
    }
}
