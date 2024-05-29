<?php

namespace App\Modules\Finance\Expense\Application\Query;

use App\Modules\Finance\Expense\Application\QueryHandler\FetchMonthlyExpenseHandler;
use App\Shared\Application\Messenger\Query;

/**
 * @see FetchMonthlyExpenseHandler
 */
final readonly class FetchMonthlyExpense implements Query
{
    public function __construct(
        public string $ownerApiToken,
        public int $month,
    ) {}
}