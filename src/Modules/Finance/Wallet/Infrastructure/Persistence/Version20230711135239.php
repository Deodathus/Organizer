<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711135239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create wallet_owners table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table wallet_owners (
                id BINARY(36) not null unique,
                external_id BINARY(36) not null,
                wallet_id BINARY(36) not null,
                created_at DATETIME default NOW() not null,
                primary key (id),
                foreign key (wallet_id) references wallets(id)
            );
            create index id_wallet_id_index on wallet_owners(id, wallet_id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            drop index id_wallet_id_index on wallet_owners;
            drop table wallet_owners;
        SQL);
    }
}
