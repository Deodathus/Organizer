<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Assigner;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;

interface RecipeToItemAssignerInterface
{
    /**
     * @param Recipe[] $recipes
     */
    public function assignMany(array $recipes, Item $item): void;
}
