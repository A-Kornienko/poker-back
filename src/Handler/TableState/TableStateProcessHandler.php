<?php

declare(strict_types=1);

namespace App\Handler\TableState;

use App\Repository\TableRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TableStateProcessHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TableRepository $tableRepository,
        protected UpdateTableStateHandler $updateTableStateHandler,
    ) {
    }

    public function __invoke(int $offset, int $limit, int $startTime): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->entityManager->clear();
            $tables = $this->tableRepository->findBy(
                criteria: ['isArchived' => false],
                limit: $limit,
                offset: $offset
            );

            foreach ($tables as $table) {
                $table = $this->tableRepository->find($table->getId());

                $dateTime = (new DateTime())->format('H:i:s');
                try {
                    $this->entityManager->getConnection()->connect();
                    ($this->updateTableStateHandler)($table);
                    file_put_contents(__DIR__ . '/succes_log.txt', $dateTime . ' - ' . $table->getState() . ' is success for id: ' . $table->getId() . "\n\n", FILE_APPEND);
                    if ($table->getTableUsers()->count() > 0 && $startTime <= time()) {
                        $this->entityManager->getConnection()->close();
                    }
                    continue;
                    //                    $tableLog['status'] = 'success';
                } catch (\Throwable $e) {
                    if ($e->getCode() !== 2000) {
                        file_put_contents(__DIR__ . '/error_log.txt', $dateTime . ' - ' . $e->getMessage() . ': ' . $e->getTraceAsString() . ' for id: ' . $table->getId() . "\n\n");
                        continue;
                    }
        
                    file_put_contents(__DIR__ . '/our_log.txt', $dateTime . ' - ' . $e->getMessage() . ' for id: ' . $table->getId() . "\n\n", FILE_APPEND);
                    $this->entityManager->getConnection()->close();
                    continue;
                }
            }

            sleep(1);
        }
    }
}
