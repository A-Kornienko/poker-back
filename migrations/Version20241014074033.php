<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014074033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE `table_setting` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL,
            image VARCHAR(255) DEFAULT NULL, currency VARCHAR(15) DEFAULT NULL, type VARCHAR(20) DEFAULT \'cash\' NOT NULL,
            buy_in INT DEFAULT 0 NOT NULL, small_blind NUMERIC(10, 2) DEFAULT \'0.1\' NOT NULL,
            big_blind NUMERIC(10, 2) DEFAULT \'0.2\' NOT NULL, style VARCHAR(20) NOT NULL,
            count_players INT DEFAULT 10 NOT NULL, rule VARCHAR(255) DEFAULT \'Texas Holdem\' NOT NULL, rake DOUBLE PRECISION DEFAULT \'0.05\' NOT NULL,
            rake_cap DOUBLE PRECISION DEFAULT \'3\' NOT NULL,
            turn_time INT DEFAULT 60 NOT NULL, time_bank JSON DEFAULT NULL,
            created_at INT DEFAULT 0 NOT NULL, updated_at INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `table_setting`');
    }
}
