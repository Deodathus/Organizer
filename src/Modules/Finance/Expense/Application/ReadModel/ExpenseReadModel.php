<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\ReadModel;

use App\Modules\Finance\Expense\Application\ViewModel\MonthlyExpense;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Shared\Application\Result\PaginatedResult;

interface ExpenseReadModel
{
    public function fetchByOwner(
        ExpenseOwnerId $ownerId,
        int $page,
        int $perPage
    ): PaginatedResult;

    /**
     * @param array<ExpenseId> $expenseIds
     *
     * @return MonthlyExpense[]
     */
    public function fetchMonthlyExpenseByIds(
        array $expenseIds,
        int       $month,
    ): array;
}
