<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\ValueObject;

final readonly class WalletCurrency
{
    public function __construct(
        public WalletCurrencyId $currencyId,
        public string $currencyCode
    ) {
    }
}
