<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use App\Modules\Minecraft\Item\Enum\ItemTypes;
use App\Modules\Minecraft\Item\Repository\FluidCellRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FluidCellRepository::class)]
class FluidCell extends Item
{
    // change ItemTypes value if you will change this
    protected const DISCRIMINATOR_NAME = 'fluidCell';

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $fluidName;

    public function __construct(string $name, int $key, ?int $subKey, ?string $itemTag, string $fluidName)
    {
        $this->fluidName = $fluidName;

        parent::__construct($name, $key, $subKey, $itemTag);
    }

    public function getDiscriminator(): string
    {
        return ItemTypes::FLUID_CELL->value;
    }

    public function getFluidName(): string
    {
        return $this->fluidName;
    }
}