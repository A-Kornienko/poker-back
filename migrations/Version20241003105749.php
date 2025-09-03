<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003105749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE winner ADD table_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EA55D929A FOREIGN KEY (table_user_id) REFERENCES `table_user` (id)');
        $this->addSql('CREATE INDEX IDX_CF6600EA55D929A ON winner (table_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EA55D929A');
        $this->addSql('DROP INDEX IDX_CF6600EA55D929A ON winner');
        $this->addSql('ALTER TABLE winner DROP table_user_id');
    }
}
