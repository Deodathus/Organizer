<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Command\StoreWallet;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\StoreWalletRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class StoreWalletController
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

    public function __invoke(StoreWalletRequest $request): JsonResponse
    {
        $walletId = $this->commandBus->dispatch(
            new StoreWallet()
        );

        return new JsonResponse(
            [
                'id' => $walletId,
            ],
            Response::HTTP_CREATED
        );
    }
}
