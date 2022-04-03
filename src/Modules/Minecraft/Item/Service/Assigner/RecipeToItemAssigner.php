<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Assigner;

use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;
use Doctrine\Common\Collections\ArrayCollection;

final class RecipeToItemAssigner implements RecipeToItemAssignerInterface
{
    public function assignMany(array $recipes, Item $item): void
    {
        $item->updateRecipes(new ArrayCollection($recipes));

        /** @var Recipe $recipe */
        foreach ($recipes as $recipe) {
        }
    }
}
