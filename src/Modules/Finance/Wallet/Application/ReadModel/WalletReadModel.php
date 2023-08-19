<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ReadModel;

use App\Modules\Finance\Wallet\Application\ViewModel\WalletViewModel;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Domain\ValueObject\WalletId;

interface WalletReadModel
{
    /**
     * @return WalletViewModel[]
     */
    public function fetchAll(WalletOwnerExternalId $ownerId, int $perPage, int $page): array;

    public function fetchOne(WalletId $walletId, WalletOwnerExternalId $ownerId): WalletViewModel;
}
