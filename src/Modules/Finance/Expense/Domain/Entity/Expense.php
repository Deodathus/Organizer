<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Entity;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseAmount;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;

final readonly class Expense
{
    private function __construct(
        private ExpenseId $id,
        private ExpenseOwnerId $ownerId,
        private ExpenseCategoryId $categoryId,
        private ExpenseAmount $amount,
        private ?string $comment = null
    ) {}

    public static function create(
        ExpenseOwnerId $ownerId,
        ExpenseCategoryId $categoryId,
        ExpenseAmount $amount,
        ?string $comment = null
    ): self {
        return new self(
            ExpenseId::generate(),
            $ownerId,
            $categoryId,
            $amount,
            $comment
        );
    }

    public static function recreate(
        ExpenseId $expenseId,
        ExpenseOwnerId $ownerId,
        ExpenseCategoryId $categoryId,
        ExpenseAmount $amount,
        ?string $comment = null
    ): self {
        return new self(
            $expenseId,
            $ownerId,
            $categoryId,
            $amount,
            $comment
        );
    }

    public function getId(): ExpenseId
    {
        return $this->id;
    }

    public function getOwnerId(): ExpenseOwnerId
    {
        return $this->ownerId;
    }

    public function getCategoryId(): ExpenseCategoryId
    {
        return $this->categoryId;
    }

    public function getAmount(): ExpenseAmount
    {
        return $this->amount;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
