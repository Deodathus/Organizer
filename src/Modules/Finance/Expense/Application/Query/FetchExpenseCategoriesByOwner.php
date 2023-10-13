<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Query;

use App\Shared\Application\Messenger\Query;

final readonly class FetchExpenseCategoriesByOwner implements Query
{
    public function __construct(
        public string $ownerApiToken
    ) {}
}
