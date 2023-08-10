<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\DTO;

final readonly class TransactionCreator
{
    public function __construct(
        public string $apiToken
    ) {}
}
