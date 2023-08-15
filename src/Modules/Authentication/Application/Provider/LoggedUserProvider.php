<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Application\Provider;

interface LoggedUserProvider
{
    public function store(): void;
}
