<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class CannotFindWalletCreatorIdentityException extends \Exception
{
    public static function withToken(string $apiToken): self
    {
        return new self(
            sprintf('Cannot find wallet creator identity! Creator token: "%s"', $apiToken)
        );
    }
}
