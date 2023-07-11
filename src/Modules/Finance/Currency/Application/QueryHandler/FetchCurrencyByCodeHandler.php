<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\QueryHandler;

use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Shared\Application\Messenger\QueryHandler;

final class FetchCurrencyByCodeHandler implements QueryHandler
{
    public function __construct(
        private readonly CurrencyRepository $repository
    ) {}

    public function __invoke(FetchCurrencyByCode $currencyQuery): CurrencyDTO
    {
        $currency = $this->repository->fetchByCode(CurrencyCode::from($currencyQuery->currencyCode->name));

        return new CurrencyDTO(
            $currency->getId()->toString(),
            $currency->getCode()->value
        );
    }
}
