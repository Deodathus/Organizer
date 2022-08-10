<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220810143347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            ALTER TABLE items 
                ADD item_tag VARCHAR(255) DEFAULT NULL, 
                ADD discriminator VARCHAR(255) NOT NULL, 
                ADD fluid_name VARCHAR(255) DEFAULT NULL
        ');

        $this->addSql('CREATE INDEX KEY_SUBKEY_INDEX ON items (`key`, sub_key)');
        $this->addSql('CREATE INDEX DISCRIMINATOR_INDEX ON items (discriminator)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX KEY_SUBKEY_INDEX ON items');
        $this->addSql('DROP INDEX DISCRIMINATOR_INDEX ON items');
        $this->addSql('ALTER TABLE items DROP item_tag, DROP discriminator, DROP fluid_name');
    }
}
