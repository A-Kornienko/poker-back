<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022090353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `tournament_setting` (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT \'paid\' NOT NULL, entry_sum NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, entry_chips NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, start_count_players INT DEFAULT 0 NOT NULL, break_settings JSON DEFAULT NULL, blind_setting JSON DEFAULT NULL, buy_in_settings JSON DEFAULT NULL, rule VARCHAR(255) DEFAULT \'Texas Holdem\' NOT NULL, prize_rule TEXT DEFAULT NULL, limit_members INT DEFAULT NULL, table_synchronization TINYINT(1) DEFAULT 0 NOT NULL, rake DOUBLE PRECISION DEFAULT \'0.05\' NOT NULL, min_count_members INT DEFAULT 0 NOT NULL, turn_time INT DEFAULT 60 NOT NULL, time_bank JSON DEFAULT NULL, created_at INT DEFAULT 0 NOT NULL, updated_at INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `tournament_setting`');
    }
}
