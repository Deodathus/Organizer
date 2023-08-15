<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Http\Controller;

use App\Modules\Authentication\Application\Query\GetMe;
use App\Modules\Authentication\Infrastructure\Http\Request\GetMeRequest;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class GetMeController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(GetMeRequest $request): JsonResponse
    {
        $me = $this->queryBus->handle(
            new GetMe($request->token)
        );

        return new JsonResponse(
            [
                'me' => $me,
            ],
            Response::HTTP_OK
        );
    }
}
