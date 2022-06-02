<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use App\Modules\Minecraft\Item\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe implements RecipeInterface
{
    #[ORM\GeneratedValue]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string',  length: 255)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'usedInRecipes', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'recipes_ingredients')]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeResult::class, cascade: ['persist', 'remove'])]
    private Collection $results;

    public function __construct(string $name, ArrayCollection $ingredients, ArrayCollection $results)
    {
        $this->name = $name;
        $this->ingredients = $ingredients;
        $this->results = $results;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * @return Collection<RecipeResult>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }
}
