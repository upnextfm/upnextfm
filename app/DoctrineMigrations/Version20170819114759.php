<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170819114759 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video_log (id INT AUTO_INCREMENT NOT NULL, video_id INT DEFAULT NULL, room_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_75A6ADC129C1004E (video_id), INDEX IDX_75A6ADC154177093 (room_id), INDEX IDX_75A6ADC1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, created_by_user_id INT DEFAULT NULL, created_in_room_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, codename VARCHAR(50) NOT NULL, provider VARCHAR(50) NOT NULL, seconds INT NOT NULL, num_plays INT NOT NULL, date_created DATETIME NOT NULL, date_last_played DATETIME NOT NULL, INDEX IDX_7CC7DA2C7D182D95 (created_by_user_id), INDEX IDX_7CC7DA2CBE205ADC (created_in_room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video_log ADD CONSTRAINT FK_75A6ADC129C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE video_log ADD CONSTRAINT FK_75A6ADC154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE video_log ADD CONSTRAINT FK_75A6ADC1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CBE205ADC FOREIGN KEY (created_in_room_id) REFERENCES room (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video_log DROP FOREIGN KEY FK_75A6ADC129C1004E');
        $this->addSql('DROP TABLE video_log');
        $this->addSql('DROP TABLE video');
    }
}
