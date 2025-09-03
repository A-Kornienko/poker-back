<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702122241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` CHANGE `game_style` `rule` VARCHAR(15) NOT NULL');

        $this->addSql(
            "
            UPDATE `table`
            SET `rule` = CASE
                WHEN `rule` = 't' THEN 'Texas Holdem'
                WHEN `rule` = 'o' THEN 'Omaha High'
                ELSE `rule`
            END
        "
        );
        $this->addSql(
            "
            UPDATE `table`
            SET `type` = CASE
                WHEN `type` = 't' THEN 'tournament'
                WHEN `type` = 'c' THEN 'cash'
                WHEN `type` = 's' THEN 'cash'
                ELSE `type`
            END
        "
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table` CHANGE `rule` `game_style` VARCHAR(15) NOT NULL');
    }
}
