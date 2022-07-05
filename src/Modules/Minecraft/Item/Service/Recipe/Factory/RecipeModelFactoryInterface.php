<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Recipe\Factory;

use App\Modules\Minecraft\Item\Entity\Recipe;
use App\Modules\Minecraft\Item\Response\Model\RecipeModel;

interface RecipeModelFactoryInterface
{
    public function build(Recipe $recipe): RecipeModel;
}
