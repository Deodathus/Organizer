<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Command;

use App\Modules\Finance\Expense\Application\CommandHandler\StoreExpenseCategoryHandler;
use App\Shared\Application\Messenger\Command;

/** @see StoreExpenseCategoryHandler */
final readonly class StoreExpenseCategory implements Command
{
    public function __construct(
        public readonly string $ownerApiToken,
        public readonly string $name
    ) {
    }
}
