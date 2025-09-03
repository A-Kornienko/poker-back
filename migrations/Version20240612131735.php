<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612131735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank ADD session VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `table` ADD session VARCHAR(255) DEFAULT NULL, CHANGE round round VARCHAR(255) DEFAULT \'pending\' NOT NULL');
        $this->addSql('ALTER TABLE winner ADD session VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `bank` DROP session');
        $this->addSql('ALTER TABLE winner DROP session');
        $this->addSql('ALTER TABLE `table` DROP session, CHANGE round round VARCHAR(255) DEFAULT \'finish\' NOT NULL');
    }
}
