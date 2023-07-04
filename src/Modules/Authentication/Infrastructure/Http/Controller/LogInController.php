<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Http\Controller;

use App\Modules\Authentication\Application\Command\LogInUser;
use App\Modules\Authentication\Infrastructure\Http\Request\LogInUserRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class LogInController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {}

    public function __invoke(LogInUserRequest $request): JsonResponse
    {
        $this->commandBus->dispatch(
            new LogInUser($request->token)
        );

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
