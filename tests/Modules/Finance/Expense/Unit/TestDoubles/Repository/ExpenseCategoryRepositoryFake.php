<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Unit\TestDoubles\Repository;

use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseCategoryDoesNotExist;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;

final class ExpenseCategoryRepositoryFake implements ExpenseCategoryRepository
{
    /** @var ExpenseCategory[] $stored */
    public function __construct(
        private array $stored = []
    ) {}

    public function store(ExpenseCategory $category): void
    {
        $this->stored[$category->getId()->toString()] = $category;
    }

    public function fetchById(ExpenseCategoryId $id): ExpenseCategory
    {
        foreach ($this->stored as $storedCategory) {
            if ($storedCategory->getId()->toString() === $id->toString()) {
                return $storedCategory;
            }
        }

        throw ExpenseCategoryDoesNotExist::withId($id->toString());
    }
}
