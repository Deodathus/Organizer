<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Query\FetchAllWallets;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\FetchAllWalletsRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchAllWalletsController
{
    public function __construct(
        private QueryBus $queryBus
    ) {}

    public function __invoke(FetchAllWalletsRequest $request): JsonResponse
    {
        return new JsonResponse(
            [
                'items' => $this->queryBus->handle(
                    new FetchAllWallets($request->requesterToken)
                ),
            ]
        );
    }
}
