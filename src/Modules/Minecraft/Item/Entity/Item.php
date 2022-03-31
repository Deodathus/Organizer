<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Modules\Minecraft\Item\Repository\ItemRepository;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $subId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name;

    public function __construct(string $name, ?int $subId)
    {
        $this->id = 1;
        $this->name = $name;
        $this->subId = $subId;
    }

    public function update(string $name, ?int $subId): void
    {
        $this->name = $name;

        if ($subId) {
            $this->subId = $subId;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubId(): ?int
    {
        return $this->subId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
