<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Adapter;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Exception\ExternalUserCannotBeFetched;
use App\Modules\Authentication\Application\Exception\ExternalUserDoesNotExist;
use App\Modules\Authentication\Application\Repository\ExternalUserRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

final class OrganizerAuthUserRepository implements ExternalUserRepository
{
    private const LINK_TEMPLATE = '%s/user/%s';

    public function __construct(
        private readonly string $organizerAuthLink,
        private readonly LoggerInterface $logger
    ) {
    }

    public function fetchById(string $externalUserId): ExternalUserDTO
    {
        $client = new Client();

        try {
            $response = $client->get(
                sprintf(
                    self::LINK_TEMPLATE,
                    $this->organizerAuthLink,
                    $externalUserId
                )
            );

            $responseContent = json_decode($response->getBody()->getContents());
            if (!property_exists($responseContent->message, '_user_id')) {
                throw ExternalUserCannotBeFetched::create();
            }

            return new ExternalUserDTO(
                $externalUserId,
                $responseContent->message->_token,
                $responseContent->message->_refresh_token
            );
        } catch (GuzzleException $exception) {
            $this->logger->error($exception->getMessage());

            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                throw ExternalUserDoesNotExist::withId($externalUserId);
            }

            throw $exception;
        }
    }
}
