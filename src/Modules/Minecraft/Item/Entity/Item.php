<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Modules\Minecraft\Item\Repository\ItemRepository;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\Table(name: 'items')]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: '`key`', type: 'integer', nullable: false)]
    private int $key;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subKey;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    public function __construct(string $name, int $key, ?int $subKey)
    {
        $this->name = $name;
        $this->key = $key;
        $this->subKey = $subKey;
    }

    public function update(string $name, int $key, ?int $subKey): void
    {
        $this->name = $name;
        $this->key = $key;

        if ($subKey) {
            $this->subKey = $subKey;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function getSubKey(): ?int
    {
        return $this->subKey;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
