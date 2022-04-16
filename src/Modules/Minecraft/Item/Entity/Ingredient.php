<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use App\Modules\Minecraft\CraftCalculator\Entity\Ingredient as IngredientInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
class Ingredient implements IngredientInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'asIngredients')]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    private Item $item;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'ingredients')]
    private Collection $usedInRecipes;

    #[Pure]
    public function __construct(int $amount, Item $item)
    {
        $this->amount = $amount;
        $this->item = $item;

        $this->usedInRecipes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    #[Pure]
    public function getItemId(): int
    {
        return $this->item->getId();
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getUsedInRecipes(): Collection
    {
        return $this->usedInRecipes;
    }

    public function updateUsedInRecipes(Collection $usedInRecipes): void
    {
        $this->usedInRecipes = $usedInRecipes;
    }

    public function addRecipeUsedIn(Recipe $recipe): void
    {
        $this->usedInRecipes->add($recipe);
    }
}
