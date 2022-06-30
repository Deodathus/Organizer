<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Controller;

use App\Modules\Google\YouTube\Adapter\YouTubeClient;
use App\Modules\Google\YouTube\DTO\ClientConfiguration\ClientAccessTokenDTO;
use App\Modules\Google\YouTube\Exception\ClientDoesNotExist;
use App\Modules\Google\YouTube\Request\YouTubeClient\StoreAccessTokenRequest;
use App\Modules\Google\YouTube\Service\ClientAccessTokenPersister;
use App\Modules\Google\YouTube\Service\ClientConfigurationFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class AccessTokenController extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ClientConfigurationFetcher $clientConfigurationFetcher,
        private readonly YouTubeClient $client,
        private readonly ClientAccessTokenPersister $tokenPersister
    ) {}

    public function store(StoreAccessTokenRequest $request): Response
    {
        $clientSid = $this->requestStack->getSession()->get('clientSid');
        if (!$clientSid) {
            return new JsonResponse(
                [
                    'error' => 'Client sid is empty!'
                ]
            );
        }

        try {
            $clientConfiguration = $this->clientConfigurationFetcher->fetchBySid($clientSid);
        } catch (ClientDoesNotExist $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ]
            );
        }

        $accessToken = $this->client->fetchAccessTokenByCode($request->code, $clientConfiguration);

        $this->tokenPersister->persist(
            new ClientAccessTokenDTO(
                accessToken: $accessToken->getAccessToken(),
                expiresIn: $accessToken->getExpiresIn(),
                refreshToken: $accessToken->getRefreshToken(),
                scope: $accessToken->getScope(),
                tokenType: $accessToken->getTokenType(),
                created: $accessToken->getCreated()
            ),
            $clientConfiguration
        );

        return new Response('Token was created successfully!');
    }
}
