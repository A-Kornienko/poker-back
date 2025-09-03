<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240731075715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament ADD COLUMN blind_coefficient INT DEFAULT 0 NOT NULL AFTER blind_speed');
        $this->addSql('ALTER TABLE tournament ADD COLUMN last_blind_update INT DEFAULT 0 NOT NULL AFTER blind_coefficient');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament DROP COLUMN blind_coefficient ');
        $this->addSql('ALTER TABLE tournament DROP COLUMN last_blind_update ');
    }
}
