<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Service;

use App\Modules\Google\YouTube\DTO\ClientConfiguration\ClientAccessTokenDTO;
use App\Modules\Google\YouTube\Entity\ClientAccessToken;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use App\Modules\Google\YouTube\Repository\ClientAccessTokenRepository;
use App\Modules\Google\YouTube\Repository\ClientConfigurationRepository;

final class ClientAccessTokenPersister
{
    public function __construct(
        private readonly ClientConfigurationRepository $clientConfigurationRepository,
        private readonly ClientAccessTokenRepository $tokenRepository
    ) {}

    public function persist(ClientAccessTokenDTO $accessTokenDTO, ClientConfiguration $clientConfiguration): void
    {
        $accessToken = new ClientAccessToken(
            accessToken: $accessTokenDTO->getAccessToken(),
            expiresIn: $accessTokenDTO->getExpiresIn(),
            refreshToken: $accessTokenDTO->getRefreshToken(),
            scope: $accessTokenDTO->getScope(),
            tokenType: $accessTokenDTO->getTokenType(),
            created: $accessTokenDTO->getCreated()
        );

        $accessToken->setClientConfiguration($clientConfiguration);
        $clientConfiguration->addAccessToken($accessToken);

        $this->tokenRepository->store($accessToken);
        $this->clientConfigurationRepository->store($clientConfiguration);
    }
}
