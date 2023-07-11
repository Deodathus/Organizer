<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class WalletDoesNotExistException extends \Exception
{
    public static function withId(string $id): self
    {
        return new self(
            sprintf('Wallet id: "%s"', $id)
        );
    }
}
