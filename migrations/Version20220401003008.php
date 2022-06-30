<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220401003008 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5C93B3A42B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worktime_entries (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, time_amount VARCHAR(255) NOT NULL, time_amount_in_minutes DOUBLE PRECISION NOT NULL, date DATE NOT NULL, INDEX IDX_3803409E166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE worktime_entries ADD CONSTRAINT FK_3803409E166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE worktime_entries DROP FOREIGN KEY FK_3803409E166D1F9C');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE worktime_entries');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
