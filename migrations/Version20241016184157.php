<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016184157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO table_setting (name, image, currency,`type`,small_blind,big_blind,style,count_players,rule,rake,rake_cap,turn_time,time_bank,count_cards)
                    SELECT `table`.name, `table`.image, `table`.currency,`table`.`type`,`table`.small_blind,`table`.big_blind,`table`.style,`table`.count_players,`table`.rule,`table`.rake,`table`.rake_cap,`table`.turn_time,`table`.time_bank,`table`.count_cards
                    FROM `table`');
        $this->addSql('ALTER TABLE `table` DROP currency, DROP type, DROP min_limit, DROP max_limit, DROP small_blind, DROP big_blind, DROP style, DROP count_players, DROP rule, DROP count_cards, DROP image, DROP rake, DROP rake_cap, DROP turn_time, DROP time_bank');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE table_setting');
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` ADD currency VARCHAR(15) DEFAULT NULL, ADD type VARCHAR(20) DEFAULT \'cash\' NOT NULL, ADD min_limit INT DEFAULT 0 NOT NULL, ADD max_limit INT DEFAULT 0 NOT NULL, ADD small_blind NUMERIC(10, 2) DEFAULT \'1.00\' NOT NULL, ADD big_blind NUMERIC(10, 2) DEFAULT \'2.00\' NOT NULL, ADD style VARCHAR(20) NOT NULL, ADD count_players INT DEFAULT 10 NOT NULL, ADD rule VARCHAR(255) DEFAULT \'Texas Holdem\' NOT NULL, ADD count_cards INT DEFAULT 0 NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD rake DOUBLE PRECISION DEFAULT \'0.05\' NOT NULL, ADD rake_cap DOUBLE PRECISION DEFAULT \'3\' NOT NULL, ADD turn_time INT DEFAULT 60 NOT NULL, ADD time_bank JSON DEFAULT NULL');
    }
}
