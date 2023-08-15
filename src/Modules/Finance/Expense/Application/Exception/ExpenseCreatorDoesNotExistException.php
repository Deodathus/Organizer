<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\Exception;

final class ExpenseCreatorDoesNotExistException extends \Exception
{
    public static function withToken(string $token): self
    {
        return new self(
            sprintf(
                'Expense category creator with given token does not exist! Given token: "%s"',
                $token
            )
        );
    }
}
