<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\ValueObject;

final readonly class ExpenseAmount
{
    public function __construct(
        public string $amount,
        public string $currencyCode
    ) {}

    public static function create(string $amount, string $currencyCode): self
    {
        return new self($amount, $currencyCode);
    }
}
