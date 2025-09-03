<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240808075329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE tournament ADD COLUMN small_blind DECIMAL(10, 2) DEFAULT 0.1 NOT NULL AFTER last_blind_update, ADD COLUMN big_blind DECIMAL(10, 2) DEFAULT 0.2 NOT NULL AFTER small_blind'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament DROP COLUMN small_blind, big_blind');
    }
}
