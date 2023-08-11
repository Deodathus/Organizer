<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Repository;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseCategoryDoesNotExist;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;

interface ExpenseCategoryRepository
{
    public function store(ExpenseCategory $category): void;

    /**
     * @throws ExpenseCategoryDoesNotExist
     */
    public function fetchById(ExpenseCategoryId $id): ExpenseCategory;
}
