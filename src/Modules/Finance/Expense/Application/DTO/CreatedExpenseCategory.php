<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\DTO;

final readonly class CreatedExpenseCategory
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
