<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Infrastructure\Adapter;

use App\Modules\Authentication\Application\DTO\ExternalUserDTO;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;
use GuzzleHttp\Client;

final class OrganizerAuthUserRepository implements ExternalUserRepository
{
    private const LINK_TEMPLATE = '%s/user/%s';

    public function __construct(
//        private readonly string $organizerAuthLink,
        private readonly Client $client
    ) {
    }

    public function fetchById(string $externalUserId): ExternalUserDTO
    {
        $response = $this->client->get(
            sprintf(
                self::LINK_TEMPLATE,
                '$this->organizerAuthLink',
                $externalUserId
            )
        );

        dd($response);
    }
}
