<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016071522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_user RENAME COLUMN chips TO stack');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_user RENAME COLUMN stack TO chips');
    }
}
