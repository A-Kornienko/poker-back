<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806085828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tournament rank and unique key';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournament_user ADD `rank` INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT unique_tournament_rank UNIQUE (`tournament_id`, `rank`)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `tournament_user` DROP `rank`');
        $this->addSql('ALTER TABLE `tournament_user` DROP CONSTRAINT unique_tournament_rank');
    }
}
