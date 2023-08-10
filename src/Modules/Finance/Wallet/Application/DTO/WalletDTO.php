<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

final readonly class WalletDTO
{
    public function __construct(
        public string $name,
        public string $creatorId,
        public string $startBalance,
        public string $currencyId,
        public string $currencyCode
    ) {}
}
