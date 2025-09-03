<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610005123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create bank related entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `bank` 
        (
            id INT AUTO_INCREMENT NOT NULL, 
            table_id INT DEFAULT NULL, 
            bet DECIMAL(10, 2) DEFAULT 0 NOT NULL, 
            sum DECIMAL(10, 2) DEFAULT 0 NOT NULL, 
            status VARCHAR(255) DEFAULT \'inProgress\' NOT NULL, 
            created_at INT DEFAULT 0 NOT NULL, 
            updated_at INT DEFAULT 0 NOT NULL, 
            INDEX IDX_D860BF7AECFF285C (table_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE bank_user 
        (
            bank_id INT NOT NULL, 
            user_id INT NOT NULL, 
            INDEX IDX_27F384C711C8FB41 (bank_id), 
            INDEX IDX_27F384C7A76ED395 (user_id), 
            PRIMARY KEY(bank_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `bank` ADD CONSTRAINT FK_D860BF7AECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE bank_user ADD CONSTRAINT FK_27F384C711C8FB41 FOREIGN KEY (bank_id) REFERENCES `bank` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_user ADD CONSTRAINT FK_27F384C7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `bank` DROP FOREIGN KEY FK_D860BF7AECFF285C');
        $this->addSql('ALTER TABLE `bank_user` DROP FOREIGN KEY FK_27F384C711C8FB41');
        $this->addSql('ALTER TABLE `bank_user` DROP FOREIGN KEY FK_27F384C7A76ED395');

        $this->addSql('DROP TABLE `bank`');
        $this->addSql('DROP TABLE `bank_user`');
    }
}
