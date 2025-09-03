<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625192406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE table_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_id INT NOT NULL,
                session VARCHAR(255) DEFAULT NULL,
                action VARCHAR(255) NOT NULL,
                data JSON DEFAULT NULL,
                created_at INT DEFAULT 0 NOT NULL,
                updated_at INT DEFAULT 0 NOT NULL,
                INDEX IDX_25893144ECFF285C (table_id)
            )DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $this->addSql(
            'ALTER TABLE table_history ADD CONSTRAINT FK_25893144ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE table_history DROP FOREIGN KEY FK_25893144ECFF285C');
        $this->addSql('DROP TABLE table_history');
    }
}
