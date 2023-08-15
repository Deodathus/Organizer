<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Exception;

final class InvalidTransactionType extends \Exception
{
    public static function withType(string $type): self
    {
        return new self(
            sprintf('Given transaction type is invalid! Given type: "%s"', $type)
        );
    }
}
