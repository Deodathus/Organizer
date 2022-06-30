<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use App\Modules\Google\YouTube\Adapter\Client as ClientWrapper;
use App\Modules\Google\YouTube\Entity\ClientAccessToken;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use Google\Client;
use Google_Service_YouTube;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class YouTubeClient implements YouTubeClientInterface
{
    private const AUTH_REDIRECT_ROUTE = 'google.youtube.auth';

    public function __construct(
      private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function getUrlToAccessTokenGeneration(ClientConfiguration $clientConfiguration): string
    {
        return $this->prepareClient($clientConfiguration)->createAuthUrl();
    }

    public function fetchAccessTokenByCode(string $code, ClientConfiguration $clientConfiguration): AccessToken
    {
        $client = $this->prepareClient($clientConfiguration);

        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        if (!isset(
            $accessToken['access_token'],
            $accessToken['expires_in'],
            $accessToken['refresh_token'],
            $accessToken['scope'],
            $accessToken['token_type'],
            $accessToken['created'])
        ) {
            throw new \RuntimeException('Access token is invalid!');
        }

        return new AccessToken(
            accessToken: $accessToken['access_token'],
            expiresIn: $accessToken['expires_in'],
            refreshToken: $accessToken['refresh_token'],
            scope: $accessToken['scope'],
            tokenType: $accessToken['token_type'],
            created: $accessToken['created']
        );
    }

    public function getClient(ClientConfiguration $clientConfiguration, ClientAccessToken $accessToken): ClientWrapper
    {
        $client = $this->prepareClient($clientConfiguration);

        $client->setAccessToken(
            [
                'access_token' => $accessToken->getAccessToken(),
                'expires_in' => $accessToken->getExpiresIn(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'scope' => $accessToken->getScope(),
                'token_type' => $accessToken->getTokenType(),
                'created' => $accessToken->getCreated(),
            ]
        );

        return new ClientWrapper($client);
    }

    private function prepareClient(ClientConfiguration $clientConfiguration): Client
    {
        $client = new Client(
            [
                'scope' => $clientConfiguration->getScope(),
                'client_id' => $clientConfiguration->getClientId(),
                'client_secret' => $clientConfiguration->getClientSecret(),
            ]
        );

        $client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
        $client->setRedirectUri(
            $this->urlGenerator->generate(
                self::AUTH_REDIRECT_ROUTE,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
        $client->setAccessType('offline');

        return $client;
    }
}
