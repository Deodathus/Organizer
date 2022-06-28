<?php

namespace App\Service;

use App\DTO\LinkDTO;

interface LinkServiceInterface
{
    /**
     * @return LinkDTO[]
     */
    public function fetchAll(): array;
}