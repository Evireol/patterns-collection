<?php
declare(strict_types=1);

namespace Domain\Repository;

use Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
}