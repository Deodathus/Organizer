<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\DTO;

use Money\Money;

final readonly class TransactionAmount
{
    public function __construct(
        public Money $value
    ) {}
}
