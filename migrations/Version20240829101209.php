<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829101209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A95ECFF285C');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A95ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A9533D1A3E7');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A9533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A95ECFF285C');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A95ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE reform_table_queues DROP FOREIGN KEY FK_E7F37A9533D1A3E7');
        $this->addSql('ALTER TABLE reform_table_queues ADD CONSTRAINT FK_E7F37A9533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
    }
}
