<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Currency\ModuleAPI\Application\Exception\CannotFetchCurrencyException;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExistException;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class CurrencyFetcher
{
    public function __construct(
        private QueryBus $queryBus
    ) {}

    /**
     * @throws CurrencyCodeIsNotSupportedException|CurrencyDoesNotExistException
     */
    public function fetch(string $code): CurrencyDTO
    {
        $currencyCode = SupportedCurrencies::tryFrom($code);
        if ($currencyCode === null) {
            throw CurrencyCodeIsNotSupportedException::withCode($code);
        }

        try {
            return $this->queryBus->handle(
                new FetchCurrencyByCode($currencyCode)
            );
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() instanceof CannotFetchCurrencyException) {
                throw CurrencyDoesNotExistException::withCode($code);
            }

            throw $exception;
        }
    }
}
