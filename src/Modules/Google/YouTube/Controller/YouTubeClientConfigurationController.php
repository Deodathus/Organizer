<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Controller;

use App\Modules\Google\YouTube\Adapter\YouTubeClientInterface;
use App\Modules\Google\YouTube\DTO\ClientConfiguration\ClientConfigurationDTO;
use App\Modules\Google\YouTube\Exception\ClientDoesNotExist;
use App\Modules\Google\YouTube\Exception\ClientSidIsNotUnique;
use App\Modules\Google\YouTube\Request\YouTubeClient\YouTubeClientConfigurationCURequest;
use App\Modules\Google\YouTube\Service\ClientConfigurationFetcher;
use App\Modules\Google\YouTube\Service\ClientConfigurationPersister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class YouTubeClientConfigurationController extends AbstractController
{
    public function __construct(
        private readonly ClientConfigurationPersister $configurationPersister,
        private readonly ClientConfigurationFetcher $configurationFetcher,
        private readonly YouTubeClientInterface $youTubeClient,
        private readonly RequestStack $requestStack
    ) {}

    public function store(YouTubeClientConfigurationCURequest $youTubeClientCURequest): JsonResponse
    {
        try {
            $this->configurationPersister->store(
                new ClientConfigurationDTO(
                    sid: $youTubeClientCURequest->sid,
                    clientId: $youTubeClientCURequest->clientId,
                    clientSecret: $youTubeClientCURequest->clientSecret,
                    scope: $youTubeClientCURequest->scope
                )
            );
        } catch (ClientSidIsNotUnique $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    public function update(YouTubeClientConfigurationCURequest $youTubeClientCURequest): JsonResponse
    {
        return new JsonResponse();
    }

    public function generateToken(int $clientId): Response
    {
        try {
            $clientConfiguration = $this->configurationFetcher->fetchById($clientId);
        } catch (ClientDoesNotExist $exception) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $this->requestStack->getSession()->set('clientSid', $clientConfiguration->getSid());

        return new RedirectResponse($this->youTubeClient->getUrlToAccessTokenGeneration($clientConfiguration));
    }
}
