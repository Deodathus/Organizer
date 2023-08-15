<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class CurrencyCodeIsNotSupportedException extends \Exception
{
    public static function withCode(string $code): self
    {
        return new self(
            sprintf('Currency code: "%s"', $code)
        );
    }
}
