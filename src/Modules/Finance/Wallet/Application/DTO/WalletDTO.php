<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

final class WalletDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $creatorId,
        public readonly int $startBalance,
        public readonly string $currencyId,
        public readonly string $currencyCode
    ) {}
}
