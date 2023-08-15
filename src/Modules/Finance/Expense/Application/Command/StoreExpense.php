<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Command;

use App\Modules\Finance\Expense\Application\CommandHandler\StoreExpenseHandler;
use App\Shared\Application\Messenger\Command;

/** @see StoreExpenseHandler */
final readonly class StoreExpense implements Command
{
    public function __construct(
        public string $walletId,
        public string $ownerApiToken,
        public string $categoryId,
        public string $amount,
        public string $currencyCode,
        public ?string $comment = null
    ) {
    }
}
