<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Query\FetchWallet;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\FetchWalletRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FetchWalletController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(FetchWalletRequest $request): JsonResponse
    {
        return new JsonResponse([
            'items' => $this->queryBus->handle(new FetchWallet($request->walletId, $request->requesterToken)),
        ]);
    }
}
