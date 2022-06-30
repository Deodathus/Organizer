<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'google_client_access_token')]
class ClientAccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 500)]
    private string $accessToken;

    #[ORM\Column(type: 'integer')]
    private int $expiresIn;

    #[ORM\Column(type: 'string', length: 255)]
    private string $refreshToken;

    #[ORM\Column(type: 'string', length: 255)]
    private string $scope;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tokenType;

    #[ORM\Column(type: 'integer')]
    private int $created;

    #[ORM\ManyToOne(targetEntity: ClientConfiguration::class, cascade: ['persist', 'remove'], inversedBy: 'accessTokens')]
    private ClientConfiguration $clientConfiguration;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        string $refreshToken,
        string $scope,
        string $tokenType,
        int $created
    ) {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
        $this->tokenType = $tokenType;
        $this->created = $created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(int $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function setTokenType(string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function getClientConfiguration(): ClientConfiguration
    {
        return $this->clientConfiguration;
    }

    public function setClientConfiguration(ClientConfiguration $clientConfiguration): void
    {
        $this->clientConfiguration = $clientConfiguration;
    }
}
