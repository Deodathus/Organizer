<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Modules\Minecraft\Item\Repository\ItemRepository;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\Index(columns: ['key', 'sub_key'], name: 'KEY_SUBKEY_INDEX')]
#[ORM\Index(columns: ['discriminator'], name: 'DISCRIMINATOR_INDEX')]
#[ORM\InheritanceType(value: 'SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[ORM\DiscriminatorMap(
    value: [
        self::DISCRIMINATOR_NAME => self::class,
        FluidCell::DISCRIMINATOR_NAME => FluidCell::class,
        Fluid::DISCRIMINATOR_NAME => Fluid::class,
    ]
)]
#[ORM\Table(name: 'items')]
class Item
{
    // change ItemTypes value if you will change this
    protected const DISCRIMINATOR_NAME = 'item';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: '`key`', type: 'integer', nullable: false)]
    private int $key;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subKey;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $itemTag;

    protected string $discriminator = self::DISCRIMINATOR_NAME;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, mappedBy: 'items')]
    private Collection $asIngredients;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: RecipeResult::class, cascade: ['remove'])]
    private Collection $recipeResult;

    #[Pure]
    public function __construct(string $name, int $key, ?int $subKey, ?string $itemTag)
    {
        $this->name = $name;
        $this->key = $key;
        $this->subKey = $subKey;
        $this->itemTag = $itemTag;

        $this->asIngredients = new ArrayCollection();
        $this->recipeResult = new ArrayCollection();
    }

    public function update(string $name, int $key, ?int $subKey): void
    {
        $this->name = $name;
        $this->key = $key;

        if ($subKey) {
            $this->subKey = $subKey;
        }
    }

    public function addAsIngredient(Ingredient $ingredient): void
    {
        $this->asIngredients->add($ingredient);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function getSubKey(): ?int
    {
        return $this->subKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItemTag(): ?string
    {
        return $this->itemTag;
    }

    /**
     * @return ArrayCollection<Ingredient>|Collection<Ingredient>
     */
    public function getAsIngredients(): ArrayCollection|Collection
    {
        return $this->asIngredients;
    }

    /**
     * @return ArrayCollection<Recipe>|Collection<Recipe>
     */
    public function getRecipeResult(): ArrayCollection|Collection
    {
        return $this->recipeResult;
    }
}
