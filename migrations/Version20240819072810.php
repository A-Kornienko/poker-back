<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819072810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `notification` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, tournament_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, created_at INT DEFAULT 0 NOT NULL, updated_at INT DEFAULT 0 NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA33D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `notification` ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `notification` ADD CONSTRAINT FK_BF5476CA33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `notification` DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE `notification` DROP FOREIGN KEY FK_BF5476CA33D1A3E7');
        $this->addSql('DROP TABLE `notification`');
    }
}
