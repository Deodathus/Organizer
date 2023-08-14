<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Entity;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;

final readonly class ExpenseCategory
{
    private function __construct(
        private ExpenseCategoryId $id,
        private ExpenseCategoryOwnerId $categoryOwnerId,
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

    public function getId(): ExpenseCategoryId
    {
        return $this->id;
    }

    public function getCategoryOwnerId(): ExpenseCategoryOwnerId
    {
        return $this->categoryOwnerId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
