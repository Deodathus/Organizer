<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\ModuleAPI\Application\Query;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Shared\Application\Messenger\Query;

final class FetchCurrencyByCode implements Query
{
    public function __construct(
        public readonly SupportedCurrencies $currencyCode
    ) {}
}
