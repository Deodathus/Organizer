<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use Doctrine\Common\Collections\Collection;

interface RecipeInterface
{
    public function getId(): int;

    /**
     * @return Collection<IngredientInterface>
     */
    public function getIngredients(): Collection;

    /**
     * @return Collection<RecipeResultInterface>
     */
    public function getResults(): Collection;
}
