<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705101145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `tournament` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT \'paid\' NOT NULL, buy_in DECIMAL(10, 2) DEFAULT 0 NOT NULL, buy_in_limit INT DEFAULT 0 NOT NULL, stop_buy_in_date INT DEFAULT 0 NOT NULL, date_start INT DEFAULT 0 NOT NULL, start_count_players INT DEFAULT 0 NOT NULL, break_start INT DEFAULT 0 NOT NULL, break_duration INT DEFAULT 0 NOT NULL, blind_speed INT DEFAULT 0 NOT NULL, chips_multiplier INT DEFAULT 0 NOT NULL, rule VARCHAR(255) DEFAULT \'Texas Holdem\' NOT NULL, created_at INT DEFAULT 0 NOT NULL, updated_at INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_user (tournament_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA1E647733D1A3E7 (tournament_id), INDEX IDX_BA1E6477A76ED395 (user_id), PRIMARY KEY(tournament_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `tournament_prize` (id INT AUTO_INCREMENT NOT NULL, tournament_id INT NOT NULL, user_id INT DEFAULT NULL, sum DECIMAL(10, 2) DEFAULT \'0\' NOT NULL, INDEX IDX_909E1BD833D1A3E7 (tournament_id), UNIQUE INDEX UNIQ_909E1BD8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `tournament_prize` ADD CONSTRAINT FK_909E1BD833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id)');
        $this->addSql('ALTER TABLE `tournament_prize` ADD CONSTRAINT FK_909E1BD8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `table` ADD tournament_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F4633D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id)');
        $this->addSql('CREATE INDEX IDX_F6298F4633D1A3E7 ON `table` (tournament_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F4633D1A3E7');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE `tournament_prize` DROP FOREIGN KEY FK_909E1BD833D1A3E7');
        $this->addSql('ALTER TABLE `tournament_prize` DROP FOREIGN KEY FK_909E1BD8A76ED395');
        $this->addSql('DROP TABLE `tournament`');
        $this->addSql('DROP TABLE tournament_user');
        $this->addSql('DROP TABLE `tournament_prize`');
        $this->addSql('DROP INDEX IDX_F6298F4633D1A3E7 ON `table`');
        $this->addSql('ALTER TABLE `table` DROP tournament_id');
    }
}
