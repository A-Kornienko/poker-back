<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610004452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create poker table related entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `table`
            (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(64) DEFAULT NULL,
                currency VARCHAR(15) DEFAULT NULL,
                type VARCHAR(20) DEFAULT NULL,
                min_limit INT DEFAULT 0 NOT NULL,
                max_limit INT DEFAULT 0 NOT NULL,
                small_blind DECIMAL(10, 2) DEFAULT 1 NOT NULL,
                big_blind DECIMAL(10, 2)  DEFAULT 2 NOT NULL,
                max_bet DECIMAL(10, 2) DEFAULT 1 NOT NULL,
                style VARCHAR(20) NOT NULL,
                count_players INT DEFAULT 10 NOT NULL,
                game_style VARCHAR(15) NOT NULL,
                count_cards INT DEFAULT 0 NOT NULL,
                round VARCHAR(255) DEFAULT \'pending\' NOT NULL,
                dealer_place INT DEFAULT 1 NOT NULL,
                small_blind_place INT DEFAULT 2 NOT NULL,
                big_blind_place INT DEFAULT 3 NOT NULL,
                turn_place INT DEFAULT 0 NOT NULL,
                last_word INT DEFAULT 0 NOT NULL,
                cards JSON NOT NULL,
                created_at INT DEFAULT 0 NOT NULL,
                updated_at INT DEFAULT 0 NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("INSERT INTO `table`(`name`, `currency`, `type`, `min_limit`, `max_limit`, `small_blind`, `big_blind`, `max_bet`, `style`, `count_players`, `game_style`, `count_cards`, `round`, `dealer_place`, `small_blind_place`, `big_blind_place`, `turn_place`, `last_word`, `cards`, `created_at`, `updated_at`)
VALUES
('Tournaments Texas', 'USD', 't', 0, 10000, 1, 2, 0, 'table_red', 10, 't', 4, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('Tournaments Texas', 'USD', 't', 0, 10000, 1, 2, 0, 'table_red', 10, 't', 10, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('Tournaments Omaha', 'USD', 't', 0, 10000, 1, 2, 0, 'table_orange', 10, 'o', 0, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('OMaHa GaMe', 'USD', 's', 0, 10000, 1, 2, 0, 'table_blue', 10, 'o', 14, 'pending', 1, 2, 3, 0, 1800, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('TeXaS GaMe', 'USD', 's', 0, 10000, 1, 2, 0, 'table_green', 10, 't', 2, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('1Cash1 Games Texas', 'USD', 'c', 10000, 100000, 1, 2, 0, 'action-blue', 10, 't', 2, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('Турнир', 'USD', 't', 2500, 100000, 1, 2, 0, 'table_orange', 1, 't', 0, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('2OMAHA2', 'USD', 'c', 0, 10000, 1, 2, 0, 'action-green', 9, 'o', 1, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW())),
('Omaha Orange Table', 'USD', 'c', 1000, 10000, 1, 2, 0, 'table_orange', 6, 'o', 4, 'pending', 1, 2, 3, 0, 0, JSON_OBJECT(), UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()));
");

        $this->addSql('CREATE TABLE `table_user`
            (
                id INT AUTO_INCREMENT NOT NULL,
                table_id INT DEFAULT NULL,
                user_id INT DEFAULT NULL,
                place INT DEFAULT 0 NOT NULL,
                chips FLOAT DEFAULT 0 NOT NULL,
                status VARCHAR(255) DEFAULT \'pending\' NOT NULL,
                bet DECIMAL(10, 2) DEFAULT 0 NOT NULL,
                bet_type VARCHAR(255) DEFAULT NULL,
                bet_expiration_time INT DEFAULT 0 NOT NULL,
                cards JSON DEFAULT NULL,
                created_at INT DEFAULT 0 NOT NULL,
                updated_at INT DEFAULT 0 NOT NULL,
                INDEX IDX_C7459682ECFF285C (table_id),
                INDEX IDX_C7459682A76ED395 (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `table_user_invoice`
            (
                id INT AUTO_INCREMENT NOT NULL,
                table_id INT DEFAULT NULL,
                user_id INT DEFAULT NULL,
                sum DECIMAL(10, 2) DEFAULT 0 NOT NULL,
                status VARCHAR(255) DEFAULT \'pending\' NOT NULL,
                created_at INT DEFAULT 0 NOT NULL,
                updated_at INT DEFAULT 0 NOT NULL,
                INDEX IDX_7D8B988CECFF285C (table_id),
                INDEX IDX_7D8B988CA76ED395 (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `table_user` ADD CONSTRAINT FK_C7459682ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE `table_user` ADD CONSTRAINT FK_C7459682A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `table_user_invoice` ADD CONSTRAINT FK_7D8B988CECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE `table_user_invoice` ADD CONSTRAINT FK_7D8B988CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `table_user` DROP FOREIGN KEY FK_C7459682ECFF285C');
        $this->addSql('ALTER TABLE `table_user` DROP FOREIGN KEY FK_C7459682A76ED395');
        $this->addSql('ALTER TABLE `table_user_invoice` DROP FOREIGN KEY FK_7D8B988CECFF285C');
        $this->addSql('ALTER TABLE `table_user_invoice` DROP FOREIGN KEY FK_7D8B988CA76ED395');

        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE `table_user`');
        $this->addSql('DROP TABLE `table_user_invoice`');
    }
}
