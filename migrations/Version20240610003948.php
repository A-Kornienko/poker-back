<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610003948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `user` 
            (
                id INT AUTO_INCREMENT NOT NULL, 
                external_id INT DEFAULT 0 NOT NULL, 
                login VARCHAR(70) DEFAULT \'0\' NOT NULL, 
                email VARCHAR(70) NOT NULL, 
                password VARCHAR(70) NOT NULL, 
                balance DECIMAL(10, 2) DEFAULT 0 NOT NULL,
                avatar VARCHAR(255) DEFAULT \'\' NOT NULL, 
                last_login INT DEFAULT 0 NOT NULL, 
                created_at INT DEFAULT 0 NOT NULL, 
                updated_at INT DEFAULT 0 NOT NULL, 
                role VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
    }
}
