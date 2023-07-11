<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

use Doctrine\DBAL\Exception;

final class CannotFindWalletCreatorIdentityException extends Exception
{
    public static function withToken(string $apiToken): self
    {
        return new self(
            sprintf('Creator token: "%s"', $apiToken)
        );
    }
}
