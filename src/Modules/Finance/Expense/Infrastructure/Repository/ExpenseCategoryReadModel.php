<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Infrastructure\Repository;

use App\Modules\Finance\Expense\Application\ReadModel\ExpenseCategoryReadModel as ExpenseCategoryReadModelInterface;
use App\Modules\Finance\Expense\Application\ViewModel\ExpenseCategoryViewModel;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use Doctrine\DBAL\Connection;

final readonly class ExpenseCategoryReadModel implements ExpenseCategoryReadModelInterface
{
    private const DB_TABLE_NAME = 'expense_categories';

    public function __construct(
        private Connection $connection
    ) {
    }

    public function fetchAllByOwner(ExpenseCategoryOwnerId $ownerId): array
    {
        /** @var array<array{id: string, name: string}> $rawData */
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from(self::DB_TABLE_NAME, 'ec')
            ->where('ec.owner_id = :ownerId')
            ->setParameter('ownerId', $ownerId->toString())
            ->fetchAllAssociative();

        $result = [];
        foreach ($rawData as $rawRow) {
            $result[] = new ExpenseCategoryViewModel(
                $rawRow['id'],
                $rawRow['name']
            );
        }

        return $result;
    }
}
