<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Exception;

abstract class WalletApiException extends \Exception
{
    public static function withPrevious(\Throwable $previous): static
    {
        return new static($previous->getMessage(), previous: $previous);
    }
}
