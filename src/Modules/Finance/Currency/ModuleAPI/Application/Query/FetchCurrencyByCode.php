<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\ModuleAPI\Application\Query;

use App\Modules\Finance\Currency\Application\QueryHandler\FetchCurrencyByCodeHandler;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Shared\Application\Messenger\Query;

/** @see FetchCurrencyByCodeHandler */
final readonly class FetchCurrencyByCode implements Query
{
    public function __construct(
        public SupportedCurrencies $currencyCode
    ) {}
}
