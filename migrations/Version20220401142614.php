<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220401142614 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, item_id INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_6BAF7870126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE items (id INT AUTO_INCREMENT NOT NULL, `key` INT NOT NULL, sub_key INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipes_ingredients (recipe_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_761206B059D8A214 (recipe_id), INDEX IDX_761206B0933FE08C (ingredient_id), PRIMARY KEY(recipe_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_result (id INT AUTO_INCREMENT NOT NULL, item_id INT DEFAULT NULL, recipe_id INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_9E74DF32126F525E (item_id), INDEX IDX_9E74DF3259D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('ALTER TABLE recipes_ingredients ADD CONSTRAINT FK_761206B059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipes_ingredients ADD CONSTRAINT FK_761206B0933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_result ADD CONSTRAINT FK_9E74DF32126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('ALTER TABLE recipe_result ADD CONSTRAINT FK_9E74DF3259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE recipes_ingredients DROP FOREIGN KEY FK_761206B0933FE08C');
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870126F525E');
        $this->addSql('ALTER TABLE recipe_result DROP FOREIGN KEY FK_9E74DF32126F525E');
        $this->addSql('ALTER TABLE recipes_ingredients DROP FOREIGN KEY FK_761206B059D8A214');
        $this->addSql('ALTER TABLE recipe_result DROP FOREIGN KEY FK_9E74DF3259D8A214');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE items');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipes_ingredients');
        $this->addSql('DROP TABLE recipe_result');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
