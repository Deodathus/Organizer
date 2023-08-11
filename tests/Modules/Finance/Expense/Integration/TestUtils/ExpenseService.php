<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\TestUtils;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;

final readonly class ExpenseService
{
    public function __construct(
        private readonly ExpenseCategoryRepository $expenseCategoryRepository
    ) {}

    public function fetchCategoryById(ExpenseCategoryId $categoryId): ExpenseCategory
    {
        return $this->expenseCategoryRepository->fetchById($categoryId);
    }
}
