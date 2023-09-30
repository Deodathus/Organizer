<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Http\Controller;

use App\Modules\Finance\Currency\Application\Query\FetchAllCurrencies;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchAllCurrenciesController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'items' => $this->queryBus->handle(
                    new FetchAllCurrencies()
                ),
            ]
        );
    }
}
