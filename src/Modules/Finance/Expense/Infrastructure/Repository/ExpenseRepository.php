<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseRepository as ExpenseRepositoryInterface;
use Doctrine\DBAL\Connection;

final readonly class ExpenseRepository implements ExpenseRepositoryInterface
{
    private const DB_TABLE_NAME = 'expenses';

    public function __construct(
        private Connection $connection
    ) {}

    public function store(Expense $expense): void
    {
        $this->connection->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'owner_id' => ':ownerId',
                'category_id' => ':categoryId',
                'amount' => ':amount',
                'currency_code' => ':currencyCode',
                'comment' => ':comment',
            ])
            ->setParameters([
                'id' => $expense->getId()->toString(),
                'owner_id' => $expense->getOwnerId()->toString(),
                'category_id' => $expense->getCategoryId()->toString(),
                'amount' => $expense->getAmount()->amount,
                'currency_code' => $expense->getAmount()->currencyCode,
                'comment' => $expense->getComment(),
            ])
            ->executeQuery();
    }
}
