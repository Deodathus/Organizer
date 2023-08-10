<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;

interface WalletBalanceCreator
{
    public function create(string $amount, string $currencyCode): WalletBalance;
}
