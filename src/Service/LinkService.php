<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\LinkDTO;
use JetBrains\PhpStorm\Pure;

class LinkService implements LinkServiceInterface
{
    /**
     * @param string[] $links
     */
    public function __construct(private array $links)
    {
    }

    #[Pure]
    public function fetchAll(): array
    {
        $result = [];

        foreach ($this->links as $link) {
            $result[] = new LinkDTO(
                $link[0],
                $link[0],
                $link[1]
            );
        }

        return $result;
    }
}
