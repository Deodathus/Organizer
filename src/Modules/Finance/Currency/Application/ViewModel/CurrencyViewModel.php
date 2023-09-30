<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\ViewModel;

final readonly class CurrencyViewModel
{
    public function __construct(
        public string $id,
        public string $code
    ) {
    }
}
