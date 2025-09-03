<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104080711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add column state';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` add column state VARCHAR(255) DEFAULT \'init\' NOT NULL AFTER `round`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` DROP state');
    }
}
