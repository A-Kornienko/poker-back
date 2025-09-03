<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127112418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_user CHANGE seat_out seat_out INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `table` ADD COLUMN reconnect_time INT DEFAULT 120');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` DROP `reconnect_time`');
        $this->addSql('ALTER TABLE table_user CHANGE seat_out seat_out INT NOT NULL');

    }
}
