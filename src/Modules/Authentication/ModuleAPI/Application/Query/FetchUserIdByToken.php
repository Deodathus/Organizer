<?php
declare(strict_types=1);

namespace App\Modules\Authentication\ModuleAPI\Application\Query;

use App\Modules\Authentication\Application\QueryHandler\FetchUserIdByTokenHandler;
use App\Shared\Application\Messenger\Query;

/**
 * @see FetchUserIdByTokenHandler
 */
final readonly class FetchUserIdByToken implements Query
{
    public function __construct(
        public string $apiToken
    ) {}
}
