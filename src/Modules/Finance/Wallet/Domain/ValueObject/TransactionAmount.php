<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\ValueObject;

use Money\Money;

final readonly class TransactionAmount
{
    public function __construct(
        public Money $value
    ) {}

    public function toString(): string
    {
        return $this->value->getAmount();
    }
}
