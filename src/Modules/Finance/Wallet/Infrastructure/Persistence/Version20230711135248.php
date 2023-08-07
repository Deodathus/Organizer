<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711135248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create transactions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table transactions (
                id BINARY(36) unique not null,
                external_id BINARY(36),
                creator_id BINARY(36),
                wallet_id BINARY(36) not null,
                amount int not null,
                type VARCHAR(255) not null,
                created_at DATETIME default NOW() not null,
                primary key (id),
                constraint transactions_wallet_id_reference_foreign_key foreign key (wallet_id) references wallets(id)
            );
            create index wallet_id_transaction_type_index on transactions(wallet_id, type);
            create index transaction_external_id_wallet_id_index on transactions(external_id, wallet_id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            alter table transactions drop foreign key transactions_wallet_id_reference_foreign_key;
            drop index wallet_id_transaction_type_index on transactions;
            drop index transaction_external_id_wallet_id_index on transactions;
            drop table transactions;
        SQL);
    }
}
