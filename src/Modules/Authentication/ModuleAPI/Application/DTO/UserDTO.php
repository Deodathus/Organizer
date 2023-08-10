<?php
declare(strict_types=1);

namespace App\Modules\Authentication\ModuleAPI\Application\DTO;

final readonly class UserDTO
{
    public function __construct(
        public string $userId
    ) {}
}
