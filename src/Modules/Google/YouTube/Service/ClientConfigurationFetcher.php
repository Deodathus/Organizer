<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Service;

use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use App\Modules\Google\YouTube\Exception\ClientDoesNotExist;
use App\Modules\Google\YouTube\Repository\ClientConfigurationRepository;

final class ClientConfigurationFetcher
{
    public function __construct(
        private readonly ClientConfigurationRepository $configurationRepository
    ) {}

    public function fetchById(int $id): ClientConfiguration
    {
        $clientConfiguration = $this->configurationRepository->find($id);
        if (!$clientConfiguration) {
            throw new ClientDoesNotExist(
                sprintf(
                    'Cannot find configuration with given id! ID: %d',
                    $id
                )
            );
        }

        return $clientConfiguration;
    }

    public function fetchBySid(string $sid): ClientConfiguration
    {
        $clientConfiguration = $this->configurationRepository->fetchBySid($sid);
        if (!$clientConfiguration) {
            throw new ClientDoesNotExist(
                sprintf(
                    'Cannot find configuration with given sid! SID: %s',
                    $sid
                )
            );
        }

        return $clientConfiguration;
    }
}
