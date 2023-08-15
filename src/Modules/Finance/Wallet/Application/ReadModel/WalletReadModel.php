<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ReadModel;

use App\Modules\Finance\Wallet\Application\ViewModel\WalletViewModel;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;

interface WalletReadModel
{
    /**
     * @return WalletViewModel[]
     */
    public function fetchAll(WalletOwnerExternalId $ownerId): array;
}
