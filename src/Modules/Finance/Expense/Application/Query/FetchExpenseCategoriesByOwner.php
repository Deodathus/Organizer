<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Query;

use App\Modules\Finance\Expense\Application\QueryHandler\FetchExpenseCategoriesByOwnerHandler;
use App\Shared\Application\Messenger\Query;

/**
 * @see FetchExpenseCategoriesByOwnerHandler
 */
final readonly class FetchExpenseCategoriesByOwner implements Query
{
    public function __construct(
        public string $ownerApiToken
    ) {
    }
}
