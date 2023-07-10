<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Currency\Integration\TestUtils;

use App\Modules\Finance\Currency\Application\ReadModel\CurrencyReadModel;
use App\Modules\Finance\Currency\Application\ViewModel\CurrencyViewModel;
use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;

final class CurrencyService
{
    public function __construct(
        private readonly CurrencyReadModel $currencyReadModel,
        private readonly CurrencyRepository $currencyRepository
    ) {}

    public function fetchCurrencyById(CurrencyId $currencyId): CurrencyViewModel
    {
        return $this->currencyReadModel->fetch($currencyId);
    }

    public function storeCurrency(CurrencyCode $code): Currency
    {
        $currency = Currency::create($code);

        $this->currencyRepository->store($currency);

        return $currency;
    }
}
