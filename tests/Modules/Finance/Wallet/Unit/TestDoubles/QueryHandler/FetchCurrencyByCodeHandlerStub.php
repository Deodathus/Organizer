<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler;

use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Exception\CannotFetchCurrencyException;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Shared\Application\Messenger\QueryHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class FetchCurrencyByCodeHandlerStub implements QueryHandler
{
    public function __construct(
        private ?string $currencyId = null
    ) {}

    public function __invoke(FetchCurrencyByCode $query): CurrencyDTO
    {
        if ($this->currencyId === null) {
            throw new HandlerFailedException(
                new Envelope(new \stdClass()),
                [
                    CannotFetchCurrencyException::withCode($query->currencyCode->value)
                ]
            );
        }

        return new CurrencyDTO(
            $this->currencyId,
            $query->currencyCode->value
        );
    }
}
