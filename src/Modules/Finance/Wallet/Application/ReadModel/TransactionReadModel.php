<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ReadModel;

use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Result\PaginatedResult;
use App\Shared\Domain\ValueObject\WalletId;

interface TransactionReadModel
{
    public function fetchByWallet(
        WalletOwnerExternalId $ownerId,
        WalletId $walletId,
        int $perPage,
        int $page
    ): PaginatedResult;
}
