<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use App\Modules\Google\YouTube\Adapter\Client as ClientWrapper;
use App\Modules\Google\YouTube\Entity\ClientAccessToken;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;
use Google\Client;
use Google\Service\YouTube;
use Google_Service_YouTube;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class YouTubeClient implements YouTubeClientInterface
{
    private const AUTH_REDIRECT_ROUTE = 'google.youtube.auth';

    public function __construct(
      private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function getChannelWithId(string $id): YouTube\ChannelListResponse
    {
        $client = new Client(
            [
//                'application_name' => $googleClientConfig->getApplicationName(),
//                'scope' => $googleClientConfig->getScope(),
//                'client_id' => $googleClientConfig->getClientId(),
//                'client_secret' => $googleClientConfig->getClientSecret(),
                'application_name' => 'Organizer',
                'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
                'developer_key' => 'AIzaSyAT14G9nLKx4puf7TO9H6YnFd9aNemlr4Y',
            ]
        );

        $service = new YouTube($client);

        $queryParams = [
            'id' => $id,
        ];

        return $service->channels->listChannels('contentDetails,statistics', $queryParams);
    }

    public function uploadVideo()
    {
        $client = new Client(
            [
//                'scope' => $googleClientConfig->getScope(),
//                'client_id' => $googleClientConfig->getClientId(),
//                'client_secret' => $googleClientConfig->getClientSecret(),
                'client_id' => '734671381868-gvl4sthsp3b33vrgi3ejg132v4bcl9rk.apps.googleusercontent.com',
                'client_secret' => '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',
                'scope' => 'https://www.googleapis.com/auth/youtube.upload',
            ]
        );

        $client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/api/google/youtube/auth');
        $client->setAccessType('offline');

        $accessToken = $client->fetchAccessTokenWithAuthCode('4/0AX4XfWjWEaHuqms_VxwDlU0OT8HCyK0vG3VvKa-Q-3hWSIpHD3_w9DKDxJl_IHWbaolMIw');

        dd($accessToken);

        try {
            $client->setAccessToken($accessToken);
        } catch (\InvalidArgumentException $exception) {
            return $client->createAuthUrl();
        }

        $service = new YouTube($client);

        $video = new YouTube\Video();
        $videoSnippet = new YouTube\VideoSnippet();
        $videoSnippet->setTitle('Test video title');
        $videoSnippet->setDescription('Test video description');
        $videoSnippet->setTags(['reddit', 'redditvideos']);

        $video->setSnippet($videoSnippet);

        return $service->videos->insert(
            'snippet',
            $video,
            [
                'data' => file_get_contents(__DIR__ . '/what activities have gotten ha....mp4'),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart',
            ]
        );
    }

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
