<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Exception;

final class CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException extends WalletApiException
{
}
