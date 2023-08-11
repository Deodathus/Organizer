<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository as ExpenseCategoryRepositoryInterface;
use Doctrine\DBAL\Connection;

final readonly class ExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    private const DB_TABLE_NAME = 'expense_categories';

    public function __construct(
        private Connection $connection
    ) {}

    public function store(ExpenseCategory $category): void
    {
        $this->connection->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'owner_id' => ':ownerId',
                'name' => ':name',
            ])
            ->setParameters([
                'id' => $category->getId()->toString(),
                'owner_id' => $category->getCategoryOwnerId(),
                'name' => $category->getName(),
            ])
            ->executeStatement();
    }
}
