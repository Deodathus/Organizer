<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\TestDoubles;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;

final class ExternalUserRepositoryThrowingExceptionStub implements ExternalUserRepository
{
    public function __construct(private readonly \Exception $exceptionToBeThrown) {}

    public function fetchById(string $externalUserId): ExternalUserDTO
    {
        throw new $this->exceptionToBeThrown;
    }
}
