<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022091015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament`
            DROP COLUMN `type`,
            DROP COLUMN `entry_sum`,
            DROP COLUMN `entry_chips`,
            DROP COLUMN `start_count_players`,
            DROP COLUMN `break_settings`,
            DROP COLUMN `blind_speed`,
            DROP COLUMN `blind_coefficient`,
            DROP COLUMN `buy_in_settings`,
            DROP COLUMN `rule`,
            DROP COLUMN `limit_members`,
            DROP COLUMN `table_synchronization`,
            DROP COLUMN `rake`,
            DROP COLUMN `min_count_members`,
            DROP COLUMN `turn_time`,
            DROP COLUMN `time_bank`');

        $this->addSql('ALTER TABLE `tournament` ADD setting_id INT NOT NULL ');
        $this->addSql('ALTER TABLE `tournament` ADD CONSTRAINT fk_tournament_setting_id FOREIGN KEY (setting_id) REFERENCES `tournament_setting` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament` DROP FOREIGN KEY fk_tournament_setting_id');
        $this->addSql('ALTER TABLE `tournament` DROP COLUMN setting_id');

        $this->addSql('ALTER TABLE `tournament`
            ADD `type` VARCHAR(255) DEFAULT NULL,
            ADD `entry_sum` DECIMAL(10, 2) DEFAULT NULL,
            ADD `entry_chips` INT DEFAULT NULL,
            ADD `start_count_players` INT DEFAULT NULL,
            ADD `break_settings` VARCHAR(255) DEFAULT NULL,
            ADD `blind_speed` INT DEFAULT NULL,
            ADD `blind_coefficient` DECIMAL(10, 2) DEFAULT NULL,
            ADD `buy_in_settings` VARCHAR(255) DEFAULT NULL,
            ADD `rule` TEXT DEFAULT NULL,
            ADD `status` VARCHAR(50) DEFAULT NULL,
            ADD `balance` DECIMAL(10, 2) DEFAULT NULL,
            ADD `limit_members` INT DEFAULT NULL,
            ADD `table_synchronization` TINYINT(1) DEFAULT NULL,
            ADD `rake` DECIMAL(10, 2) DEFAULT NULL,
            ADD `min_count_members` INT DEFAULT NULL,
            ADD `turn_time` INT DEFAULT NULL,
            ADD `time_bank` INT DEFAULT NULL
        ');
    }
}
