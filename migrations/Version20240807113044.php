<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807113044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE table_user CHANGE count_buy_in count_buy_in INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament DROP buy_in_limit, DROP stop_buy_in_date, DROP buy_in_chips');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table_user` CHANGE count_buy_in count_buy_in INT DEFAULT 0');
        $this->addSql('ALTER TABLE tournament ADD buy_in_limit INT DEFAULT 0 NOT NULL, ADD stop_buy_in_date INT DEFAULT 0 NOT NULL, ADD buy_in_chips INT DEFAULT 0 NOT NULL');
    }
}
