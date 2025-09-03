<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126080730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change pending round ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE `table` SET `round` = 'preFlop' WHERE `round` = 'pending'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE `table` SET `round` = 'pending' WHERE `round` = 'preFlop'");
    }
}
