<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128080210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament` add COLUMN blind_level INT DEFAULT 1');
        $this->addSql('ALTER TABLE `tournament_setting` add COLUMN  `late_registration` JSON NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament` drop column blind_level');
        $this->addSql('ALTER TABLE `tournament_setting` drop column late_registration');

    }
}
