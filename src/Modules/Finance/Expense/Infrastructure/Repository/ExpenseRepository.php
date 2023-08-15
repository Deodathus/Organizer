<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseDoesNotExist;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseRepository as ExpenseRepositoryInterface;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseAmount;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use Doctrine\DBAL\Connection;

final readonly class ExpenseRepository implements ExpenseRepositoryInterface
{
    private const DB_TABLE_NAME = 'expenses';

    public function __construct(
        private Connection $connection
    ) {
    }

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
                'ownerId' => $expense->getOwnerId()->toString(),
                'categoryId' => $expense->getCategoryId()->toString(),
                'amount' => $expense->getAmount()->amount,
                'currencyCode' => $expense->getAmount()->currencyCode,
                'comment' => $expense->getComment(),
            ])
            ->executeQuery();
    }

    public function fetchById(ExpenseId $expenseId): Expense
    {
        /** @var array{id: string, owner_id: string, category_id: string, amount: string, currency_code: string, comment: string}|false $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'owner_id', 'category_id', 'amount', 'currency_code', 'comment')
            ->from(self::DB_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $expenseId->toString())
            ->fetchAssociative();

        if (!$rawData) {
            throw ExpenseDoesNotExist::withId($expenseId->toString());
        }

        return Expense::recreate(
            ExpenseId::fromString($rawData['id']),
            ExpenseOwnerId::fromString($rawData['owner_id']),
            ExpenseCategoryId::fromString($rawData['category_id']),
            ExpenseAmount::create($rawData['amount'], $rawData['currency_code']),
            $rawData['comment']
        );
    }
}
