<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Factory;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\Recipe;

interface RecipeFactoryInterface
{
    /**
     * @param Item[] $usedItems
     */
    public function build(StoreRecipeDTO $recipeDTO, array $usedItems): Recipe;
}
