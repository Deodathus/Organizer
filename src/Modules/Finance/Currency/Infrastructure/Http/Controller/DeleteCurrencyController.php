<?php

declare(strict_types=1);

namespace App\Modules\Finance\Currency\Infrastructure\Http\Controller;

use App\Modules\Finance\Currency\Application\Command\DeleteCurrency;
use App\Modules\Finance\Currency\Infrastructure\Http\Request\DeleteCurrencyRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class DeleteCurrencyController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(DeleteCurrencyRequest $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new DeleteCurrency($request->id)
        );

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
