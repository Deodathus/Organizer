<?php

namespace App\Modules\Minecraft\Item\Contract\Recipe\Service;

use App\Modules\Minecraft\Item\Entity\RecipeInterface;

interface RecipeContractInterface
{
    public function fetch(int $recipeId): RecipeInterface;
}
