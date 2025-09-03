<?php

declare(strict_types=1);

namespace App\Handler\Balance\Rebuy;

use App\Entity\{Table, User};
use App\Repository\TableUserInvoiceRepository;
use App\Repository\TableUserRepository;
use App\Service\TableUserInvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReBuyCashTableHandler extends AbstractRebuyBalanceHandler
{
    public function __invoke(User $user, Table $table, float $stack): void
    {
        $this->defaultBuyIn($table,$user, $stack);
    }
}
