<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Factory;

use App\Modules\Minecraft\CraftCalculator\Model\Calculable;
use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;
use App\Modules\Minecraft\Item\Entity\RecipeInterface;

final class CalculableFactory
{
    public static function fromRecipe(RecipeInterface $recipe): CalculableInterface
    {
        return new Calculable($recipe);
    }
}
