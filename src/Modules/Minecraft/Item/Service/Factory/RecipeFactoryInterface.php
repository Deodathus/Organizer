<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Factory;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Item;
use App\Modules\Minecraft\Item\Entity\RecipeInterface;

interface RecipeFactoryInterface
{
    /**
     * @param Item[] $usedItems
     */
    public function build(StoreRecipeDTO $recipeDTO, array $usedItems): RecipeInterface;
}
