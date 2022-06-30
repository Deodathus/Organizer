<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Service;

use App\Modules\Google\YouTube\DTO\ClientConfiguration\ClientConfigurationDTO;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use App\Modules\Google\YouTube\Exception\ClientSidIsNotUnique;
use App\Modules\Google\YouTube\Repository\ClientConfigurationRepository;

final class ClientConfigurationPersister
{
    public function __construct(
        private readonly ClientConfigurationRepository $configurationRepository
    ) {}

    public function store(ClientConfigurationDTO $configurationDTO): void
    {
        $sidIsNotUnique = $this->configurationRepository->fetchCountBySid($configurationDTO->sid) > 0;

        if ($sidIsNotUnique) {
            throw new ClientSidIsNotUnique(
                sprintf('SID: %s is not unique!', $configurationDTO->sid)
            );
        }

        $this->configurationRepository->store(
            new ClientConfiguration(
                sid: $configurationDTO->sid,
                scope: $configurationDTO->scope,
                clientId: $configurationDTO->clientId,
                clientSecret: $configurationDTO->clientSecret
            )
        );
    }
}
