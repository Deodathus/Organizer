<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;
use App\Modules\Minecraft\Item\Entity\Recipe;

interface RecipeServiceInterface
{
    public function fetch(int $id): Recipe;

    public function store(StoreRecipeDTO $recipeDTO): int;
}
