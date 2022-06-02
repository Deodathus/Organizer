<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Entity;

interface RecipeResultInterface
{
    public function getId(): int;

    public function getAmount(): int;

    public function getItemId(): int;

    public function getItemName(): string;
}
