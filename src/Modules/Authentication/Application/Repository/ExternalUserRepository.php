<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\Repository;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;

interface ExternalUserRepository
{
    public function fetchById(string $externalUserId): ExternalUserDTO;
}
