<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Application\CommandHandler;

use App\Modules\Finance\Currency\Application\Command\StoreCurrency;
use App\Modules\Finance\Currency\Application\DTO\CreatedCurrency;
use App\Modules\Finance\Currency\Application\Exception\CurrencyWithGivenCodeAlreadyExistsException;
use App\Modules\Finance\Currency\Application\Exception\UnsupportedCurrencyCodeException;
use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Exception\CurrencyWIthGivenCodeAlreadyExists as CurrencyWIthGivenCodeAlreadyExistsDomainException;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Shared\Application\Messenger\CommandHandler;

final class StoreCurrencyHandler implements CommandHandler
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository
    ) {}

    public function __invoke(StoreCurrency $storeCurrency): CreatedCurrency
    {
        $currencyCode = CurrencyCode::tryFrom(strtolower($storeCurrency->code));
        if ($currencyCode === null) {
            throw UnsupportedCurrencyCodeException::withCode($storeCurrency->code);
        }

        $currency = Currency::create($currencyCode);

        try {
            $this->currencyRepository->store($currency);
        } catch (CurrencyWIthGivenCodeAlreadyExistsDomainException $exception) {
            throw CurrencyWithGivenCodeAlreadyExistsException::withCode($currencyCode->value);
        }

        return new CreatedCurrency($currency->getId()->toString());
    }
}
