<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\Exception;

final class CurrencyDoesNotExistException extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Currency id: "%s"', $id));
    }
}
