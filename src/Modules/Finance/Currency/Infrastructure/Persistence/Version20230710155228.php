<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710155228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create currencies table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            create table currencies (
                id BINARY(36) not null unique,
                code VARCHAR(255) not null unique,
                primary key (id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            drop table currencies
        ');
    }
}
