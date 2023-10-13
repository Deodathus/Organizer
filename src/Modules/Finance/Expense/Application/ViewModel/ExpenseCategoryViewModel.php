<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\ViewModel;

final readonly class ExpenseCategoryViewModel
{
    public function __construct(
        public string $id,
        public string $name
    ) {}
}
