<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Query;

use App\Modules\Finance\Expense\Application\QueryHandler\FetchExpensesByOwnerHandler;
use App\Shared\Application\Messenger\Query;

/**
 * @see FetchExpensesByOwnerHandler
 */
final readonly class FetchExpensesByOwner implements Query
{
    public function __construct(
        public string $ownerApiToken,
        public int $page,
        public int $perPage
    ) {
    }
}
