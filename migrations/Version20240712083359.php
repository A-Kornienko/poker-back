<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712083359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament ADD date_start_registration INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD date_end_registration INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD balance NUMERIC(10, 2) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD limit_members INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tournament DROP date_start_registration');
        $this->addSql('ALTER TABLE tournament DROP date_end_registration');
        $this->addSql('ALTER TABLE tournament DROP balance');
        $this->addSql('ALTER TABLE tournament DROP limit_members');
    }
}
