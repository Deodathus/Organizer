<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Application\ReadModel\ExpenseCategoryReadModel;
use App\Modules\Finance\Expense\Application\ReadModel\ExpenseReadModel as ExpenseReadModelInterface;
use App\Modules\Finance\Expense\Application\ViewModel\ExpenseCategoryViewModel;
use App\Modules\Finance\Expense\Application\ViewModel\ExpenseViewModel;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Shared\Application\Result\PaginatedResult;
use Doctrine\DBAL\Connection;

final readonly class ExpenseReadModel implements ExpenseReadModelInterface
{
    private const DB_TABLE_NAME = 'expenses';

    public function __construct(
        private Connection $connection,
        private ExpenseCategoryReadModel $expenseCategoryReadModel
    ) {
    }

    public function fetchByOwner(
        ExpenseOwnerId $ownerId,
        int $page,
        int $perPage
    ): PaginatedResult {
        /** @var array<int, array{id: string, category_id: string, amount: string, currency_code: string, comment: string, created_at: string}> $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'category_id', 'amount', 'currency_code', 'comment', 'created_at')
            ->from(self::DB_TABLE_NAME)
            ->where('owner_id = :ownerId')
            ->orderBy('created_at', 'DESC')
            ->setParameter('ownerId', $ownerId->toString())
            ->setMaxResults($perPage)
            ->setFirstResult($page * $perPage - $perPage)
            ->fetchAllAssociative();

        /** @var array<string, ExpenseCategoryViewModel> $categories */
        $categories = $this->expenseCategoryReadModel->fetchAllByOwner(
            ExpenseCategoryOwnerId::fromString(
                $ownerId->toString()
            )
        );

        $result = [];
        foreach ($rawData as $rawRow) {
            $result[] = ExpenseViewModel::create(
                $rawRow['id'],
                $categories[$rawRow['category_id']]->name,
                $rawRow['amount'],
                $rawRow['currency_code'],
                $rawRow['comment'],
                new \DateTimeImmutable($rawRow['created_at'])
            );
        }

        return new PaginatedResult(
            $result,
            $this->fetchExpensesCountByOwner($ownerId)
        );
    }

    private function fetchExpensesCountByOwner(ExpenseOwnerId $ownerId): int
    {
        /** @var array{count: int}|false $rawResult */
        $rawResult = $this->connection->createQueryBuilder()
            ->select('count(id) as count')
            ->from(self::DB_TABLE_NAME)
            ->where('owner_id = :ownerId')
            ->setParameter('ownerId', $ownerId->toString())
            ->fetchAssociative();

        if (!$rawResult) {
            return 0;
        }

        return $rawResult['count'];
    }
}
