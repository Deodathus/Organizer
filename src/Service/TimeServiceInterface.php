<?php

namespace App\Service;

interface TimeServiceInterface
{
    /**
     * @return string
     */
    public function getCurrentTime(): string;
}
