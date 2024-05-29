<?php

namespace App\Modules\Finance\Expense\Application\ViewModel;

final readonly class MonthlyExpense
{
    private function __construct(
        public string $month,
        public string $amount,
        public string $currencyCode,
    ) {}

    public static function create(
        int $month,
        string $amount,
        string $currencyCode,
    ): self
    {
        return new self(
            $month,
            $amount,
            $currencyCode,
        );
    }

    /**
     * @return array{month: string, amount: string, currencyCode: string}
     */
    public function toArray(): array
    {
        return [
            'month' => $this->month,
            'amount' => $this->amount,
            'currencyCode' => $this->currencyCode,
        ];
    }
}