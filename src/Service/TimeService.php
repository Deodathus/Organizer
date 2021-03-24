<?php

namespace App\Service;

class TimeService implements TimeServiceInterface
{
    public function getCurrentTime(): string
    {
        return date('l, H:i');
    }
}
