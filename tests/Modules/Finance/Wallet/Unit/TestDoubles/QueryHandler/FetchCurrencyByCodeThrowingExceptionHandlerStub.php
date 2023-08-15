<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler;

use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Shared\Application\Messenger\QueryHandler;

final class FetchCurrencyByCodeThrowingExceptionHandlerStub implements QueryHandler
{
    public function __construct(
        private readonly \Exception $exceptionToBeThrown
    ) {
    }

    public function __invoke(FetchCurrencyByCode $query): CurrencyDTO
    {
        throw $this->exceptionToBeThrown;
    }
}
