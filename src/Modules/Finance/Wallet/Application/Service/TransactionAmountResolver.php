<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;

interface TransactionAmountResolver
{
    public function resolve(string $amount, string $currencyCode): TransactionAmount;
}
