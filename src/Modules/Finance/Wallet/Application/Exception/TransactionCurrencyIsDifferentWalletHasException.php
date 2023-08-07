<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class TransactionCurrencyIsDifferentWalletHasException extends \Exception
{
    public static function withCurrenciesCodes(\Throwable $previous): self
    {
        return new self($previous->getMessage(), 0, $previous);
    }
}
