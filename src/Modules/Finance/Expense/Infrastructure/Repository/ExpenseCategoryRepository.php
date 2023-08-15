<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseCategoryDoesNotExist;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository as ExpenseCategoryRepositoryInterface;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use Doctrine\DBAL\Connection;

final readonly class ExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    private const DB_TABLE_NAME = 'expense_categories';

    public function __construct(
        private Connection $connection
    ) {
    }

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
                'ownerId' => $category->getCategoryOwnerId(),
                'name' => $category->getName(),
            ])
            ->executeStatement();
    }

    public function fetchById(ExpenseCategoryId $id): ExpenseCategory
    {
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'owner_id')
            ->from(self::DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw ExpenseCategoryDoesNotExist::withId($id->toString());
        }

        return ExpenseCategory::recreate(
            ExpenseCategoryId::fromString($rawData['id']),
            ExpenseCategoryOwnerId::fromString($rawData['owner_id']),
            $rawData['name']
        );
    }
}
