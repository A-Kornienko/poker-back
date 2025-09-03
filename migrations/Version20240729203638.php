<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729203638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trigger to update table_id in tournament_user when it changes in table_user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TRIGGER update_tournament_user_table_id
            AFTER UPDATE ON table_user
            FOR EACH ROW
            BEGIN
                IF (SELECT tournament_id FROM `table` WHERE id = NEW.table_id) IS NOT NULL 
                   AND NEW.table_id <> OLD.table_id THEN
                    UPDATE tournament_user
                    SET table_id = NEW.table_id
                    WHERE user_id = NEW.user_id AND table_id = OLD.table_id;
                END IF;
            END;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS update_tournament_user_table_id');
    }
}
