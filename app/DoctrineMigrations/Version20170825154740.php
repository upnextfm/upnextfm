<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170825154740 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE room_settings (id INT AUTO_INCREMENT NOT NULL, is_public TINYINT(1) NOT NULL, thumb_sm VARCHAR(255) NOT NULL, thumb_md VARCHAR(255) NOT NULL, thumb_lg VARCHAR(255) NOT NULL, join_message TEXT DEFAULT NULL, date_updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room ADD settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B59949888 FOREIGN KEY (settings_id) REFERENCES room_settings (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B59949888 ON room (settings_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B59949888');
        $this->addSql('DROP TABLE room_settings');
        $this->addSql('DROP INDEX UNIQ_729F519B59949888 ON room');
        $this->addSql('ALTER TABLE room DROP settings_id');
    }
}
