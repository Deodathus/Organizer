<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711135236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create wallets table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table wallets (
                id BINARY(36) not null unique,
                name VARCHAR(255) not null,
                balance VARCHAR(255) not null,
                currency_id BINARY(36) not null,
                currency_code VARCHAR(255) not null,
                created_at DATETIME default NOW() not null,
                primary key (id)
            );
            create index wallet_name_wallet_id_index on wallets(name, id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            drop index name_id_index on wallets;
            drop table currencies;
        SQL);
    }
}
