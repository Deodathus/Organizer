<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Command\StoreWallet;
use App\Modules\Finance\Wallet\Application\DTO\CreatedWallet;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\StoreWalletRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreWalletController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(StoreWalletRequest $request): JsonResponse
    {
        /** @var CreatedWallet $walletId */
        $walletId = $this->commandBus->dispatch(
            new StoreWallet(
                $request->name,
                $request->creatorToken,
                $request->currencyCode,
                $request->balance
            )
        );

        return new JsonResponse(
            [
                'id' => $walletId->walletId,
            ],
            Response::HTTP_CREATED
        );
    }
}
