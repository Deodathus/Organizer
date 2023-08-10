<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\ModuleAPI\Application\DTO;

final class CurrencyDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $code
    ) {}
}
