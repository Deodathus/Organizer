<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Exception;

final class CannotRegisterTransferTransactionWithoutReceiverWalletIdException extends \Exception
{
    public static function create(): self
    {
        return new self();
    }
}
