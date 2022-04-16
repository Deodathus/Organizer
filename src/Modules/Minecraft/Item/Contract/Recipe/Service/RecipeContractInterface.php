<?php

namespace App\Modules\Minecraft\Item\Contract\Recipe\Service;

use App\Modules\Minecraft\Item\Entity\Recipe;

interface RecipeContractInterface
{
    public function fetch(int $recipeId): Recipe;
}