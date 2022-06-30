<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630113442 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE google_client_access_token (
            id INT AUTO_INCREMENT NOT NULL, 
            client_configuration_id INT DEFAULT NULL, 
            access_token VARCHAR(500) NOT NULL, 
            expires_in INT NOT NULL, 
            refresh_token VARCHAR(255) NOT NULL, 
            scope VARCHAR(255) NOT NULL, 
            token_type VARCHAR(255) NOT NULL, 
            created INT NOT NULL, 
            INDEX IDX_A2C90CA0573AA823 (client_configuration_id), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('CREATE TABLE google_client_configuration (
            id INT AUTO_INCREMENT NOT NULL, 
            sid VARCHAR(255) NOT NULL, 
            scope VARCHAR(255) NOT NULL, 
            client_id VARCHAR(255) NOT NULL, 
            client_secret VARCHAR(255) NOT NULL, 
            UNIQUE INDEX UNIQ_3049848457167AB4 (sid), 
            INDEX IDX_3049848457167AB4 (sid), 
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE google_client_access_token ADD CONSTRAINT FK_A2C90CA0573AA823 FOREIGN KEY (client_configuration_id) REFERENCES google_client_configuration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE google_client_access_token DROP FOREIGN KEY FK_A2C90CA0573AA823');
        $this->addSql('DROP TABLE google_client_access_token');
        $this->addSql('DROP TABLE google_client_configuration');
    }
}
