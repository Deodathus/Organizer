<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'asIngredients')]
    private Collection $items;

    #[ORM\ManyToMany(targetEntity: Recipe::class, mappedBy: 'ingredients')]
    private Collection $usedInRecipes;

    #[Pure]
    public function __construct(int $amount, Collection $items)
    {
        $this->amount = $amount;
        $this->items = $items;

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

    public function getItemId(): int
    {
        return $this->items->first()->getId();
    }

    public function getItem(): Item
    {
        return $this->items->first();
    }

    /**
     * @return Collection<Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return Collection<Recipe>
     */
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

    public function getAsRecipeResult(): array
    {
        return $this->getItem()->getRecipeResult()->toArray();
    }

    public function getItemName(): string
    {
        return $this->getItem()->getName();
    }
}
