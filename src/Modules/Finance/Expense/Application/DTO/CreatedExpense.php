<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\DTO;

final readonly class CreatedExpense
{
    public function __construct(
        public readonly string $expenseId,
    ) {
    }
}
