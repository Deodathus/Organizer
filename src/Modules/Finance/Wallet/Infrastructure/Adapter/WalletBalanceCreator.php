<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Adapter;

use App\Modules\Finance\Wallet\Application\Service\WalletBalanceCreator as WalletBalanceCreatorInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use Money\Currency;
use Money\Money;

final class WalletBalanceCreator implements WalletBalanceCreatorInterface
{
    public function create(string $amount, string $currencyCode): WalletBalance
    {
        return new WalletBalance(new Money($amount, new Currency($currencyCode)));
    }
}
