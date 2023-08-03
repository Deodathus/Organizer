<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler;

use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchCurrencyByCodeHandlerStub implements QueryHandler
{
    public function __construct(
        private string $currencyId
    ) {}

    public function __invoke(FetchCurrencyByCode $query): CurrencyDTO
    {
        return new CurrencyDTO(
            $this->currencyId,
            $query->currencyCode->value
        );
    }
}
