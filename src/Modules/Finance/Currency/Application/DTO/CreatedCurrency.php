<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\DTO;

final readonly class CreatedCurrency
{
    public function __construct(
        public string $currencyId
    ) {
    }
}
