<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\TestDoubles;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;
use Ramsey\Uuid\Uuid;

final class ExternalUserRepositoryFake implements ExternalUserRepository
{
    public function fetchById(string $externalUserId): ExternalUserDTO
    {
        return new ExternalUserDTO(
            $externalUserId,
            Uuid::uuid4()->toString(),
            Uuid::uuid4()->toString()
        );
    }
}
