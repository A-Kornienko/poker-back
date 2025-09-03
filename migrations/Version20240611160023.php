<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611160023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE winner 
            (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT DEFAULT NULL, 
                table_id INT DEFAULT NULL, 
                bank_id INT DEFAULT NULL, 
                sum DECIMAL(10, 2) NOT NULL, 
                created_at INT DEFAULT 0 NOT NULL, 
                updated_at INT DEFAULT 0 NOT NULL, 
                INDEX IDX_CF6600EA76ED395 (user_id), 
                INDEX IDX_CF6600EECFF285C (table_id), 
                INDEX IDX_CF6600E11C8FB41 (bank_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600E11C8FB41 FOREIGN KEY (bank_id) REFERENCES `bank` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EA76ED395');
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EECFF285C');
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600E11C8FB41');

        $this->addSql('DROP TABLE winner');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
