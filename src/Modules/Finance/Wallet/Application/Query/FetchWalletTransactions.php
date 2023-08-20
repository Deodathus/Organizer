<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Query;

use App\Modules\Finance\Wallet\Application\QueryHandler\FetchWalletTransactionsHandler;
use App\Shared\Application\Messenger\Query;

/** @see FetchWalletTransactionsHandler */
final readonly class FetchWalletTransactions implements Query
{
    public function __construct(
        public string $walletId,
        public string $requesterToken,
        public int $perPage,
        public int $page
    ) {
    }
}
