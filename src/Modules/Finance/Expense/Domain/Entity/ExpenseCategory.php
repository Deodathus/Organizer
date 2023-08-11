<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Entity;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;

final readonly class ExpenseCategory
{
    private function __construct(
        private ExpenseCategoryId $categoryId,
        private ExpenseCategoryOwnerId $categoryCreatorId,
        private string $name
    ) {}

    public static function create(ExpenseCategoryOwnerId $categoryCreatorId, string $name): self
    {
        return new self(
            ExpenseCategoryId::generate(),
            $categoryCreatorId,
            $name
        );
    }

    public static function recreate(
        ExpenseCategoryId $categoryId,
        ExpenseCategoryOwnerId $categoryOwnerId,
        string $name
    ): self {
        return new self($categoryId, $categoryOwnerId, $name);
    }

    public function getCategoryId(): ExpenseCategoryId
    {
        return $this->categoryId;
    }

    public function getCategoryCreatorId(): ExpenseCategoryOwnerId
    {
        return $this->categoryCreatorId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
