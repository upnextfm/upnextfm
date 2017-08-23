<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170823121555 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video ADD thumb_sm VARCHAR(255) NOT NULL, ADD thumb_md VARCHAR(255) NOT NULL, ADD thumb_lg VARCHAR(255) NOT NULL, DROP thumb_small, DROP thumb_medium, DROP thumb_large');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video ADD thumb_small VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD thumb_medium VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD thumb_large VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP thumb_sm, DROP thumb_md, DROP thumb_lg');
    }
}
