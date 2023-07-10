<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629214449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            create table users (
                id BINARY(36) not null unique,
                external_id BINARY(36) not null unique,
                first_name varchar(255) not null,
                last_name varchar(255) not null,
                api_token varchar(255) not null unique,
                api_refresh_token varchar(255) not null unique,
                created_at timestamp default CURRENT_TIMESTAMP,
                primary key (id)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            drop table users
        ');
    }
}
