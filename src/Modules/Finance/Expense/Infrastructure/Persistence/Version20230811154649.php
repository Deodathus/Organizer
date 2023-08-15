<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811154649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create expenses table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            create table expenses (
                id BINARY(36) not null unique,
                owner_id BINARY(36) not null,
                category_id BINARY(36) not null,
                amount VARCHAR(255) not null,
                currency_code VARCHAR(255) not null,
                comment VARCHAR(255) default null,
                created_at DATETIME default CURRENT_TIMESTAMP
            );
            create index owner_id_category_id_expenses on expenses(owner_id, category_id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            drop index owner_id_category_id_expenses on expenses;
            drop table expenses;
        SQL);
    }
}
