<?php

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Query;

use App\Modules\Finance\Wallet\Application\QueryHandler\FetchTransactionsIdsByOwnerAndMonthHandler;
use App\Shared\Application\Messenger\Query;

/** @see FetchTransactionsIdsByOwnerAndMonthHandler */
final readonly class FetchTransactionsIdsByOwnerAndMonth implements Query
{
    public function __construct(
        public string $ownerId,
        public int $month,
    ) {}
}