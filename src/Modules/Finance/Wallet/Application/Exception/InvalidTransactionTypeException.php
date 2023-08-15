<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class InvalidTransactionTypeException extends \Exception
{
    public static function withPrevious(\Throwable $previous): self
    {
        return new self($previous->getMessage(), 0, $previous);
    }

    public static function withType(string $type): self
    {
        return new self(
            sprintf('Transaction type is invalid! Given type: "%s"', $type)
        );
    }
}
