<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170825155759 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE room_settings ADD room_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_settings ADD CONSTRAINT FK_45A3600154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_45A3600154177093 ON room_settings (room_id)');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B59949888');
        $this->addSql('DROP INDEX UNIQ_729F519B59949888 ON room');
        $this->addSql('ALTER TABLE room DROP settings_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE room ADD settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B59949888 FOREIGN KEY (settings_id) REFERENCES room_settings (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B59949888 ON room (settings_id)');
        $this->addSql('ALTER TABLE room_settings DROP FOREIGN KEY FK_45A3600154177093');
        $this->addSql('DROP INDEX UNIQ_45A3600154177093 ON room_settings');
        $this->addSql('ALTER TABLE room_settings DROP room_id');
    }
}
