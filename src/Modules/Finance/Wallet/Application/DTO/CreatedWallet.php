<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

final readonly class CreatedWallet
{
    public function __construct(
        public string $walletId
    ) {
    }
}
