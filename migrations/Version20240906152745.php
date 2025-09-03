<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906152745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank ADD rake DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE `table` ADD rake DOUBLE PRECISION DEFAULT \'0.05\' NOT NULL, ADD rake_cap DOUBLE PRECISION DEFAULT \'3\' NOT NULL, ADD rake_status TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament ADD rake DOUBLE PRECISION DEFAULT \'0.05\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `bank` DROP rake');
        $this->addSql('ALTER TABLE `table` DROP rake, DROP rake_cap, DROP rake_status');
        $this->addSql('ALTER TABLE tournament DROP rake');
    }
}
