<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716082541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` CHANGE type type VARCHAR(20) DEFAULT \'cash\' NOT NULL, CHANGE rule rule VARCHAR(255) DEFAULT \'Texas Holdem\' NOT NULL');
        $this->addSql('ALTER TABLE table_user DROP is_requested, CHANGE seat_out seat_out JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user ADD id INT AUTO_INCREMENT NOT NULL, ADD table_id INT DEFAULT NULL, CHANGE tournament_id tournament_id INT, CHANGE user_id user_id INT, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('CREATE INDEX IDX_BA1E6477ECFF285C ON tournament_user (table_id)');
        $this->addSql('ALTER TABLE user CHANGE login login VARCHAR(70) NOT NULL, CHANGE role role VARCHAR(255) DEFAULT \'ROLE_PLAYER\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `table` CHANGE type type VARCHAR(20) NOT NULL, CHANGE rule rule VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE table_user ADD is_requested TINYINT(1) NOT NULL, CHANGE seat_out seat_out JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user DROP PRIMARY KEY, DROP INDEX IDX_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE tournament_user DROP id, DROP table_id, CHANGE tournament_id tournament_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE tournament_user ADD PRIMARY KEY (tournament_id, user_id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE user CHANGE login login VARCHAR(70) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL');
    }
}
