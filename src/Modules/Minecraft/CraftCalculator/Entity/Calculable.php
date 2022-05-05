<?php

namespace App\Modules\Minecraft\CraftCalculator\Entity;

use Doctrine\Common\Collections\Collection;

interface Calculable
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
