<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Table};
use App\Repository\{BankRepository};

class BankService
{
    public function __construct(
        protected BankRepository $bankRepository,
    ) {
    }

    public function getTableBanks(Table $table): array
    {
        return $this->bankRepository->findBy(
            criteria: [
                'table'   => $table,
                'session' => $table->getSession()
            ],
            orderBy: ['sum' => 'DESC']
        );
    }
}
