<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Http\Controller;

use App\Modules\Finance\Currency\Application\Command\StoreCurrency;
use App\Modules\Finance\Currency\Application\DTO\CreatedCurrency;
use App\Modules\Finance\Currency\Infrastructure\Http\Request\StoreCurrencyRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreCurrencyController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(StoreCurrencyRequest $storeCurrencyRequest): JsonResponse
    {
        /** @var CreatedCurrency $createdCurrency */
        $createdCurrency = $this->commandBus->dispatch(
            new StoreCurrency($storeCurrencyRequest->code)
        );

        return new JsonResponse(
            [
                'id' => $createdCurrency->currencyId,
            ],
            Response::HTTP_CREATED
        );
    }
}
