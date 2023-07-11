<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Command;

use App\Shared\Application\Messenger\Command;

final class StoreWallet implements Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $creatorApiToken,
        public readonly string $currencyCode,
        public readonly int $startBalance
    ) {}
}
