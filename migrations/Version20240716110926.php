<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716110926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_user ADD UNIQUE KEY unique_table_user_place (table_id, place)');
        $this->addSql('ALTER TABLE table_user ADD UNIQUE KEY unique_table_user_id (table_id, user_id)');
        $this->addSql('ALTER TABLE tournament_user ADD UNIQUE KEY unique_tournament_user (tournament_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        // Удаление уникальных индексов
        $this->addSql('ALTER TABLE table_user DROP INDEX unique_table_user_place');
        $this->addSql('ALTER TABLE table_user DROP INDEX unique_table_user_id');
        $this->addSql('ALTER TABLE tournament_user DROP INDEX unique_tournament_user');
    }
}
