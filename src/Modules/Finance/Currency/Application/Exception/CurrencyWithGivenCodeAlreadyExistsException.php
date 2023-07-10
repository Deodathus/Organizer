<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\Exception;

final class CurrencyWithGivenCodeAlreadyExistsException extends \Exception
{
    public static function withCode(string $code): self
    {
        return new self(
            sprintf('Currency code: "%s"', $code)
        );
    }
}
