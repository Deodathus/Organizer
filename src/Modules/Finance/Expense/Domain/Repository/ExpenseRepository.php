<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Repository;

use App\Modules\Finance\Expense\Domain\Entity\Expense;

interface ExpenseRepository
{
    public function store(Expense $expense): void;
}
