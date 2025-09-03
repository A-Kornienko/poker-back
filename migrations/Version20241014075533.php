<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241014075533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` ADD setting_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_TABLE_SETTING_ID ON `table` (setting_id)');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT fk_table_setting_id FOREIGN KEY (setting_id) REFERENCES `table_setting` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_SETTING_ID ON `table`');
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY fk_table_setting_id');
        $this->addSql('ALTER TABLE `table` DROP setting_id');
    }
}
