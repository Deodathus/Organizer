<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Exception;

final class ExpenseCategoryDoesNotExistException extends \Exception
{
    public static function fromPrevious(\Throwable $previous): self
    {
        return new self($previous->getMessage(), previous: $previous);
    }
}
