<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170510073333 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks=0');
        $this->addSql('REPLACE INTO `work_package_status` (`id`, `name`, `sequence`, `visible`, `created_at`, `updated_at`)
            VALUES 
            (1, \'label.open\', 0, 1, \'2017-01-01\', \'2017-01-01\'),
            (2, \'label.pending\', 1, 1, \'2017-01-01\', \'2017-01-01\'),
            (3, \'label.ongoing\', 2, 1, \'2017-01-01\', \'2017-01-01\'),
            (4, \'label.completed\', 3, 1, \'2017-01-01\', \'2017-01-01\'),
            (5, \'label.closed\', -1, 0, \'2017-01-01\', \'2017-01-01\')
        ');
        $this->addSql('SET foreign_key_checks=1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks=0');
        $this->addSql('REPLACE INTO `work_package_status` (`id`, `name`, `sequence`, `visible`, `created_at`, `updated_at`)
            VALUES 
            (1, \'label.todo\', 0, 1, \'2017-01-01\', \'2017-01-01\'),
            (2, \'label.in_progress\', 1, 1, \'2017-01-01\', \'2017-01-01\'),
            (3, \'label.code_review\', 2, 1, \'2017-01-01\', \'2017-01-01\'),
            (4, \'label.finished\', 3, 1, \'2017-01-01\', \'2017-01-01\'),
            (5, \'label.closed\', -1, 0, \'2017-01-01\', \'2017-01-01\')
        ');
        $this->addSql('SET foreign_key_checks=1');
    }
}
