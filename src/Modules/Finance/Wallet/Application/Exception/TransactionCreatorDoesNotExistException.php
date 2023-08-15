<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class TransactionCreatorDoesNotExistException extends \Exception
{
    public static function withToken(string $token): self
    {
        return new self(
            sprintf('Transaction creator does not exist! Given API token: "%s"', $token)
        );
    }
}
