<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729082337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament ADD buy_in_settings JSON DEFAULT NULL AFTER buy_in_chips');
        $this->addSql('ALTER TABLE tournament CHANGE buy_in entry_sum DECIMAL(10, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD entry_chips DECIMAL(10, 2) DEFAULT 0 NOT NULL AFTER entry_sum');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament DROP COLUMN buy_in_settings');
        $this->addSql('ALTER TABLE tournament CHANGE entry_sum buy_in DECIMAL(10, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament DROP COLUMN entry_chips');
    }
}
