<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\QueryHandler;

use App\Modules\Finance\Currency\Application\Query\FetchAllCurrencies;
use App\Modules\Finance\Currency\Application\ReadModel\CurrencyReadModel;
use App\Modules\Finance\Currency\Application\ViewModel\CurrencyViewModel;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchAllCurrenciesQueryHandler implements QueryHandler
{
    public function __construct(
        private CurrencyReadModel $currencyReadModel
    ) {
    }

    /**
     * @return CurrencyViewModel[]
     */
    public function __invoke(FetchAllCurrencies $query): array
    {
        return $this->currencyReadModel->fetchAll();
    }
}
