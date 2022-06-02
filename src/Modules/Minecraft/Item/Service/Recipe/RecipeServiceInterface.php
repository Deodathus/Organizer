<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\RecipeInterface;

interface RecipeServiceInterface
{
    public function fetch(int $id): RecipeInterface;

    public function store(StoreRecipeDTO $recipeDTO): int;
}
