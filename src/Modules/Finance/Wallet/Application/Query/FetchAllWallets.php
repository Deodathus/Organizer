<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Query;

use App\Modules\Finance\Wallet\Application\QueryHandler\FetchAllWalletsHandler;
use App\Shared\Application\Messenger\Query;

/** @see FetchAllWalletsHandler */
final readonly class FetchAllWallets implements Query
{
    public function __construct(
        public string $requesterToken
    ) {}
}
