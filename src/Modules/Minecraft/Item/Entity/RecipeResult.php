<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use App\Modules\Minecraft\CraftCalculator\Entity\RecipeResult as RecipeResultInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class RecipeResult implements RecipeResultInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'recipeResult')]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id')]
    private Item $item;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'results')]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id')]
    private Recipe $recipe;

    public function __construct(int $amount, Item $item)
    {
        $this->amount = $amount;
        $this->item = $item;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function updateRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getItemId(): int
    {
        return $this->getItem()->getId();
    }

    public function getItemName(): string
    {
        return $this->getItem()->getName();
    }
}
