<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\ViewModel;

final class CurrencyViewModel
{
    public function __construct(
        public readonly string $id,
        public readonly string $code
    ) {}
}
