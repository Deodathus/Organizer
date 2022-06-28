<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Model;

use App\Modules\Minecraft\Item\Entity\Ingredient;
use App\Modules\Minecraft\Item\Entity\RecipeResult;
use Doctrine\Common\Collections\Collection;

interface CalculableInterface
{
    public function getId(): int;

    /**
     * @return Collection<Ingredient>
     */
    public function getIngredients(): Collection;

    /**
     * @return Collection<RecipeResult>
     */
    public function getResults(): Collection;
}
