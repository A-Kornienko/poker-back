<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240827050009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament_user` DROP FOREIGN KEY FK_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE `tournament_user` ADD CONSTRAINT FK_BA1E6477ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament_user` DROP FOREIGN KEY FK_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE `tournament_user` ADD CONSTRAINT FK_BA1E6477ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
    }
}
