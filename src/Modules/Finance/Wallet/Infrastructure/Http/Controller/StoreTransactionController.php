<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Http\Controller;

use App\Modules\Finance\Wallet\Application\Command\RegisterTransaction;
use App\Modules\Finance\Wallet\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\DTO\TransactionType;
use App\Modules\Finance\Wallet\Application\Exception\InvalidTransactionTypeException;
use App\Modules\Finance\Wallet\Infrastructure\Http\Request\StoreTransactionRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreTransactionController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(StoreTransactionRequest $request): JsonResponse
    {
        $transactionType = TransactionType::tryFrom($request->transactionType);

        if ($transactionType === null) {
            throw InvalidTransactionTypeException::withType($request->transactionType);
        }

        $this->commandBus->dispatch(
            new RegisterTransaction(
                $transactionType,
                $request->walletId,
                new TransactionAmount($request->transactionAmount, $request->transactionCurrencyCode),
                new TransactionCreator($request->transactionCreatorApiToken),
                null,
                $request->receiverWalletId
            )
        );

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
