<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241001084732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE player_setting (id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            dynamic_stack_view JSON NOT NULL, 
            card_squeeze TINYINT(1) NOT NULL, 
            button_macros JSON NOT NULL, 
            updated_at INT DEFAULT 0 NOT NULL, 
            created_at INT DEFAULT 0 NOT NULL, 
            UNIQUE INDEX UNIQ_FE8047DDA76ED395 (user_id), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player_setting DROP FOREIGN KEY FK_FE8047DDA76ED395');
        $this->addSql('DROP TABLE player_setting');
    }
}
