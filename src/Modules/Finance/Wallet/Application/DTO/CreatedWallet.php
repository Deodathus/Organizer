<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

final class CreatedWallet
{
    public function __construct(
        public readonly string $walletId
    ) {}
}
