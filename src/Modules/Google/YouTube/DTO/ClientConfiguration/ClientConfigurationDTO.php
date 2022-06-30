<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\DTO\ClientConfiguration;

final class ClientConfigurationDTO
{
    public function __construct(
        public readonly string $sid,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $scope
    ) {}
}
