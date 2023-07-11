<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerId;

final class WalletOwner
{
    private function __construct(
        private WalletOwnerId $ownerId
    ) {}
}
