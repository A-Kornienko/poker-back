<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930081213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` ADD turn_time INT DEFAULT 60 NOT NULL, ADD time_bank JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE table_user ADD time_bank JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` DROP turn_time, DROP time_bank');
        $this->addSql('ALTER TABLE `table_user` DROP time_bank');
    }
}
