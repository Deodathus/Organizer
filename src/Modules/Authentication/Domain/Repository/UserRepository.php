<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Domain\Repository;

use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\ValueObject\Token;

interface UserRepository
{
    public function register(User $user): void;

    public function fetchByToken(Token $token): void;
}
