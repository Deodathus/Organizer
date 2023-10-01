<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Adapter;

use App\Modules\Finance\Wallet\Application\Service\WalletBalanceResolver as WalletBalanceResolverInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use Money\Currency;
use Money\Money;

final class WalletBalanceResolver implements WalletBalanceResolverInterface
{
    public function resolve(string $amount, string $currencyCode): WalletBalance
    {
        return new WalletBalance(new Money($amount, new Currency($currencyCode)));
    }
}
