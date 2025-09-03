<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240813133321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        //tournaments
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE tournament_prize DROP FOREIGN KEY FK_909E1BD833D1A3E7');
        $this->addSql('ALTER TABLE tournament_prize ADD CONSTRAINT FK_909E1BD833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F4633D1A3E7');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F4633D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id) ON DELETE CASCADE');

        //table
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7AECFF285C');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EECFF285C');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE spectator DROP FOREIGN KEY FK_54C71505ECFF285C');
        $this->addSql('ALTER TABLE spectator ADD CONSTRAINT FK_54C71505ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE table_history DROP FOREIGN KEY FK_25893144ECFF285C');
        $this->addSql('ALTER TABLE table_history ADD CONSTRAINT FK_25893144ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE table_user DROP FOREIGN KEY FK_C7459682ECFF285C');
        $this->addSql('ALTER TABLE table_user ADD CONSTRAINT FK_C7459682ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE table_user_invoice DROP FOREIGN KEY FK_7D8B988CECFF285C');
        $this->addSql('ALTER TABLE table_user_invoice ADD CONSTRAINT FK_7D8B988CECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id) ON DELETE CASCADE');

        //user
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE table_user DROP FOREIGN KEY FK_C7459682A76ED395');
        $this->addSql('ALTER TABLE table_user ADD CONSTRAINT FK_C7459682A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE table_user_invoice DROP FOREIGN KEY FK_7D8B988CA76ED395');
        $this->addSql('ALTER TABLE table_user_invoice ADD CONSTRAINT FK_7D8B988CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE tournament_prize DROP FOREIGN KEY FK_909E1BD8A76ED395');
        $this->addSql('ALTER TABLE tournament_prize ADD CONSTRAINT FK_909E1BD8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EA76ED395');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        //tournaments
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id)');

        $this->addSql('ALTER TABLE tOP FOREIGN KEY FK_909E1BD833D1A3E7');
        $this->addSql('ALTER TABLE tournament_prize ADD CONSTRAINT FK_909E1BD833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id)');

        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F4633D1A3E7');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F4633D1A3E7 FOREIGN KEY (tournament_id) REFERENCES `tournament` (id)');

        //table
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477ECFF285C');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE bank DROP FOREIGN KEY FK_D860BF7AECFF285C');
        $this->addSql('ALTER TABLE bank ADD CONSTRAINT FK_D860BF7AECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EECFF285C');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE spectator DROP FOREIGN KEY FK_54C71505ECFF285C');
        $this->addSql('ALTER TABLE spectator ADD CONSTRAINT FK_54C71505ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE table_history DROP FOREIGN KEY FK_25893144ECFF285C');
        $this->addSql('ALTER TABLE table_history ADD CONSTRAINT FK_25893144ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE table_user DROP FOREIGN KEY FK_C7459682ECFF285C');
        $this->addSql('ALTER TABLE table_user ADD CONSTRAINT FK_C7459682ECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        $this->addSql('ALTER TABLE table_user_invoice DROP FOREIGN KEY FK_7D8B988CECFF285C');
        $this->addSql('ALTER TABLE table_user_invoice ADD CONSTRAINT FK_7D8B988CECFF285C FOREIGN KEY (table_id) REFERENCES `table` (id)');

        //user
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE table_user DROP FOREIGN KEY FK_C7459682A76ED395');
        $this->addSql('ALTER TABLE table_user ADD CONSTRAINT FK_C7459682A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE table_user_invoice DROP FOREIGN KEY FK_7D8B988CA76ED395');
        $this->addSql('ALTER TABLE table_user_invoice ADD CONSTRAINT FK_7D8B988CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE tournament_prize DROP FOREIGN KEY FK_909E1BD8A76ED395');
        $this->addSql('ALTER TABLE tournament_prize ADD CONSTRAINT FK_909E1BD8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE winner DROP FOREIGN KEY FK_CF6600EA76ED395');
        $this->addSql('ALTER TABLE winner ADD CONSTRAINT FK_CF6600EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }
}
