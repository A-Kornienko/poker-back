<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127142759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE `table` ADD COLUMN small_blind DECIMAL(10, 2) DEFAULT 1 NOT NULL,
                          ADD COLUMN big_blind DECIMAL(10, 2) DEFAULT 2 NOT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` DROP COLUMN small_blind, DROP COLUMN big_blind');
    }

}
