<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Command;

use App\Modules\Finance\Wallet\Application\CommandHandler\StoreWalletHandler;
use App\Shared\Application\Messenger\Command;

/** @see StoreWalletHandler */
final readonly class StoreWallet implements Command
{
    public function __construct(
        public string $name,
        public string $creatorApiToken,
        public string $currencyCode,
        public string $startBalance
    ) {
    }
}
