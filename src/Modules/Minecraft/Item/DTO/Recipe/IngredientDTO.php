<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe;

use Doctrine\Common\Collections\ArrayCollection;

final class IngredientDTO
{
    /**
     * @param ArrayCollection<IngredientItemDTO> $items
     */
    public function __construct(
        private readonly ArrayCollection $items
    ) {}

    /**
     * @return ArrayCollection<IngredientItemDTO>
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }
}
