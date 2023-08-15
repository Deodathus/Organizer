<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Repository;

use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseDoesNotExist;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;

interface ExpenseRepository
{
    public function store(Expense $expense): void;

    /**
     * @throws ExpenseDoesNotExist
     */
    public function fetchById(ExpenseId $expenseId): Expense;
}
