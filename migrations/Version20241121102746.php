<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121102746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE table_history
            ADD players JSON NOT NULL,
            ADD blinds JSON NOT NULL,
            ADD dealer INT NOT NULL,
            ADD cards JSON DEFAULT NULL,
            ADD preflop JSON DEFAULT NULL,
            ADD flop JSON DEFAULT NULL,
            ADD turn JSON DEFAULT NULL,
            ADD river JSON DEFAULT NULL,
            ADD pot JSON DEFAULT NULL,
            ADD winners JSON DEFAULT NULL,
            DROP action,
            DROP data,
            CHANGE session session VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE table_history 
            ADD action VARCHAR(255) NOT NULL, 
            ADD data LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, 
            DROP players, 
            DROP blinds, 
            DROP dealer, 
            DROP cards, 
            DROP preflop, 
            DROP flop, 
            DROP turn, 
            DROP river, 
            DROP pot, 
            DROP winners, 
            CHANGE session session VARCHAR(255) DEFAULT NULL');
    }
}
