<?php

declare(strict_types=1);

namespace App\DTO;

class LinkDTO
{
    public function __construct(private string $name, private string $description, private string $url)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
