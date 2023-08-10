<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\DTO;

final class CreatedCurrency
{
    public function __construct(
        public readonly string $currencyId
    ) {}
}
