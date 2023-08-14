<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Persistence;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230811154531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create expense_categories table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            create table expense_categories (
                id BINARY(36) not null unique,
                owner_id BINARY(36) not null,
                name VARCHAR(255) not null
            );
            create index owner_id_expense_categories on expense_categories(owner_id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            drop index owner_id_expense_categories on expense_categories;
            drop table expense_categories;
        SQL);
    }
}
