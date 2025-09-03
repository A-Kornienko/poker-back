<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007074753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP COLUMN `balance`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD balance NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');
    }
}
