<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class TransactionCreatorDoesNotOwnWalletException extends \Exception
{
    public static function withId(\Throwable $previous): self
    {
        return new self($previous->getMessage(), 0, $previous);
    }
}
