<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Entity;

use App\Modules\Google\YouTube\Repository\ClientConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientConfigurationRepository::class)]
#[ORM\Table(name: 'google_client_configuration')]
#[ORM\Index(fields: ['sid'])]
class ClientConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $sid;

    #[ORM\Column(type: 'string', length: 255)]
    private string $scope;

    #[ORM\Column(type: 'string', length: 255)]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $clientSecret;

    #[ORM\OneToMany(mappedBy: 'clientConfiguration', targetEntity: ClientAccessToken::class)]
    private Collection $accessTokens;

    public function __construct(string $sid, string $scope, string $clientId, string $clientSecret)
    {
        $this->sid = $sid;
        $this->scope = $scope;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->accessTokens = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSid(): string
    {
        return $this->sid;
    }

    public function setSid(string $sid): void
    {
        $this->sid = $sid;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getAccessTokens(): ArrayCollection|Collection
    {
        return $this->accessTokens;
    }

    public function getAccessToken(): ClientAccessToken
    {
        return $this->accessTokens->first();
    }

    public function addAccessToken(ClientAccessToken $accessToken): void
    {
        $this->accessTokens->add($accessToken);
    }
}
