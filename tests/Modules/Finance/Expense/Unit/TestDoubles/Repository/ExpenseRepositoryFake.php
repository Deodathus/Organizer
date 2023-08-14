<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Unit\TestDoubles\Repository;

use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseDoesNotExist;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;

final class ExpenseRepositoryFake implements ExpenseRepository
{
    /** @var Expense[] $stored */
    public function __construct(
        private array $stored = []
    ) {}

    public function store(Expense $expense): void
    {
        $this->stored[$expense->getId()->toString()] = $expense;
    }

    public function fetchById(ExpenseId $expenseId): Expense
    {
        foreach ($this->stored as $storedExpense) {
            if ($storedExpense->getId()->toString() === $expenseId->toString()) {
                return $storedExpense;
            }
        }

        throw ExpenseDoesNotExist::withId($expenseId->toString());
    }
}
