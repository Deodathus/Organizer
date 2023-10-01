<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Adapter;

use App\Modules\Finance\Wallet\Application\Service\TransactionAmountResolver as TransactionAmountResolverInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use Money\Currency;
use Money\Money;

final readonly class TransactionAmountResolver implements TransactionAmountResolverInterface
{
    public function resolve(string $amount, string $currencyCode): TransactionAmount
    {
        return new TransactionAmount(new Money((int) $amount, new Currency($currencyCode)));
    }
}
