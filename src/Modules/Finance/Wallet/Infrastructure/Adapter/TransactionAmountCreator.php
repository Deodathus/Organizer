<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Adapter;

use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator as MoneyAmountCreatorInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use Money\Currency;
use Money\Money;

final readonly class TransactionAmountCreator implements MoneyAmountCreatorInterface
{
    public function create(string $amount, string $currencyCode): TransactionAmount
    {
        return new TransactionAmount(new Money($amount, new Currency($currencyCode)));
    }
}
