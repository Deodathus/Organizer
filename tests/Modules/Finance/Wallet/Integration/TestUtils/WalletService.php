<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;

final readonly class WalletService
{
    public function __construct(
        private CurrencyRepository $currencyRepository
    ) {}

    public function storeCurrency(CurrencyCode $code): Currency
    {
        $currency = Currency::create($code);

        $this->currencyRepository->store($currency);

        return $currency;
    }
}
