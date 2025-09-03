<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023143348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update setting_id to 1 where it is NULL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            UPDATE `table`
            SET `setting_id` = CASE
                WHEN `id` = 1 THEN 1
                WHEN `id` = 2 THEN 2
                WHEN `id` = 3 THEN 3
                WHEN `id` = 4 THEN 4
                WHEN `id` = 5 THEN 5
                WHEN `id` = 6 THEN 6
                WHEN `id` = 7 THEN 7
                WHEN `id` = 8 THEN 8
                WHEN `id` = 9 THEN 9
                ELSE `setting_id`
            END
            WHERE `id` IN (1, 2, 3, 4, 5, 6, 7, 8, 9);
        '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
        UPDATE `table`
        SET `setting_id` = NULL
        WHERE `id` IN (1, 2, 3, 4, 5, 6, 7, 8, 9);
    ');
    }
}
