<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Domain\Exception;

final class ExpenseCategoryDoesNotExist extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf(
                'Expense category with given id does not exist! Given id: "%s"', $id
            )
        );
    }
}
