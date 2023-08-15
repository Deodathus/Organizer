<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerId;

final readonly class WalletOwner
{
    private function __construct(
        public WalletOwnerId $ownerId,
        public WalletOwnerExternalId $externalId
    ) {
    }

    public static function create(WalletOwnerExternalId $externalId): self
    {
        return new self(
            WalletOwnerId::generate(),
            $externalId
        );
    }

    public static function reproduce(WalletOwnerId $walletOwnerId, WalletOwnerExternalId $externalId): self
    {
        return new self($walletOwnerId, $externalId);
    }
}
