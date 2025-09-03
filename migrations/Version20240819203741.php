<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819203741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reform_table_queues ADD tournament_id INT NOT NULL, ADD status VARCHAR(255) NOT NULL, ADD data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A95ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A9533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('CREATE INDEX IDX_E7F37A95ECFF285C ON reform_table_queues (table_id)');
        $this->addSql('CREATE INDEX IDX_E7F37A9533D1A3E7 ON reform_table_queues (tournament_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A95ECFF285C');
        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A9533D1A3E7');
        $this->addSql('DROP INDEX IDX_E7F37A95ECFF285C ON reform_table_queues');
        $this->addSql('DROP INDEX IDX_E7F37A9533D1A3E7 ON reform_table_queues');
        $this->addSql('ALTER TABLE reform_table_queues DROP tournament_id, DROP status, DROP data');
    }
}
