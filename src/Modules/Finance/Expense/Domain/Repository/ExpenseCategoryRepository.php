<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Repository;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;

interface ExpenseCategoryRepository
{
    public function store(ExpenseCategory $category): void;
}
