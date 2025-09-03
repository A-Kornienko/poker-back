<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108153957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_setting CHANGE currency currency VARCHAR(15) DEFAULT \'USD\' NOT NULL');
        $this->addSql('ALTER TABLE tournament_setting ADD name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table_setting` CHANGE currency currency VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE `tournament_setting` DROP name');
    }
}
