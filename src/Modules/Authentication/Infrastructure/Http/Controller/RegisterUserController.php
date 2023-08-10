<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Http\Controller;

use App\Modules\Authentication\Application\Command\RegisterUser;
use App\Modules\Authentication\Infrastructure\Http\Request\RegisterUserRequest;
use App\Shared\Application\Messenger\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegisterUserController
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

    public function __invoke(RegisterUserRequest $registerUserRequest): JsonResponse
    {
        $this->commandBus->dispatch(new RegisterUser($registerUserRequest->userId));

        return new JsonResponse(
            null,
            Response::HTTP_CREATED,
        );
    }
}
