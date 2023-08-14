<?php
declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Exception;

final class ExpenseDoesNotExist extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf(
                'Expense with given id does not exist! Given id: "%s"',
                $id
            )
        );
    }
}
