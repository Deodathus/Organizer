<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\ViewModel;

final readonly class ExpenseViewModel
{
    private const CREATED_AT_FORMAT = 'd-m H:i';

    private function __construct(
        public string $id,
        public string $categoryName,
        public string $amount,
        public string $currencyCode,
        public ?string $comment,
        public string $createdAt
    ) {
    }

    public static function create(
        string $id,
        string $categoryName,
        string $amount,
        string $currencyCode,
        ?string $comment,
        \DateTimeImmutable $createdAt
    ): self {
        return new self(
            $id,
            $categoryName,
            $amount,
            $currencyCode,
            $comment,
            $createdAt->format(self::CREATED_AT_FORMAT)
        );
    }
}
