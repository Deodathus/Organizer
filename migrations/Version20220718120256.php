<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718120256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE ingredient_item (
                ingredient_id INT NOT NULL, 
                item_id INT NOT NULL, 
                INDEX IDX_414A9588933FE08C (ingredient_id), 
                INDEX IDX_414A9588126F525E (item_id), 
                PRIMARY KEY(ingredient_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('
            ALTER TABLE ingredient_item 
                ADD CONSTRAINT FK_414A9588933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE
        ');

        $this->addSql('
            ALTER TABLE ingredient_item 
                ADD CONSTRAINT FK_414A9588126F525E FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE
        ');

        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870126F525E');
        $this->addSql('DROP INDEX IDX_6BAF7870126F525E ON ingredient');
        $this->addSql('ALTER TABLE ingredient DROP item_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ingredient_item');
        $this->addSql('ALTER TABLE ingredient ADD item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('CREATE INDEX IDX_6BAF7870126F525E ON ingredient (item_id)');
    }
}
