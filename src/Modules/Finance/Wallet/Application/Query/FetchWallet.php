<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Query;

use App\Modules\Finance\Wallet\Application\QueryHandler\FetchWalletHandler;
use App\Shared\Application\Messenger\Query;

/** @see FetchWalletHandler */
final readonly class FetchWallet implements Query
{
    public function __construct(
        public string $walletId,
        public string $requesterToken
    ) {
    }
}
