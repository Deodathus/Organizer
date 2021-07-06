<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331111608 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_entity (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_time_entry_entity ADD project_entity_id INT NOT NULL');
        $this->addSql('ALTER TABLE work_time_entry_entity ADD CONSTRAINT FK_8E32F7E59019388A FOREIGN KEY (project_entity_id) REFERENCES project_entity (id)');
        $this->addSql('CREATE INDEX IDX_8E32F7E59019388A ON work_time_entry_entity (project_entity_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_time_entry_entity DROP FOREIGN KEY FK_8E32F7E59019388A');
        $this->addSql('DROP TABLE project_entity');
        $this->addSql('DROP INDEX IDX_8E32F7E59019388A ON work_time_entry_entity');
        $this->addSql('ALTER TABLE work_time_entry_entity DROP project_entity_id');
    }
}
