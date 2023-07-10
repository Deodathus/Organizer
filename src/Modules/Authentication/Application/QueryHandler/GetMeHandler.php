<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\QueryHandler;

use App\Modules\Authentication\Application\Query\GetMe;
use App\Shared\Application\Messenger\QueryHandler;

final class GetMeHandler implements QueryHandler
{
    public function __invoke(GetMe $getMe): array
    {
        return [
            'token' => $getMe->token,
        ];
    }
}
