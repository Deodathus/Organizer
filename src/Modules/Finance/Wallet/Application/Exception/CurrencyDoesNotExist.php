<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class CurrencyDoesNotExist extends \Exception
{
    public static function withCode(string $code): self
    {
        return new self(
            sprintf('Currency with given code does not exist! Given code: %s', $code)
        );
    }
}
