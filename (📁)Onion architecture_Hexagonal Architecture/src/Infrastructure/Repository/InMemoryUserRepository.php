<?php
declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var User[] */
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->email] = $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }

    // Для демонстрации: можно вывести всех пользователей
    public function getAll(): array
    {
        return array_values($this->users);
    }
}