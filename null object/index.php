<?php

declare(strict_types=1);

interface User
{
    public function getName(): string;
    public function hasAccess(): bool;
}

class RealUser implements User
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasAccess(): bool
    {
        return true;
    }
}

class GuestUser implements User
{
    public function getName(): string
    {
        return 'Гость';
    }

    public function hasAccess(): bool
    {
        return false;
    }
}

class UserRepository
{
    /**
     * @var array<int, RealUser>
     */
    private array $users;

    public function __construct()
    {
        $this->users = [
            1 => new RealUser('Анна'),
            2 => new RealUser('Борис'),
        ];
    }

    public function find(int $id): User
    {
        return $this->users[$id] ?? new GuestUser();
    }
}

// --- Использование ---
$repo = new UserRepository();

$user1 = $repo->find(1);
echo $user1->getName() . " — доступ: " . ($user1->hasAccess() ? 'да' : 'нет') . PHP_EOL;

$user2 = $repo->find(999); // не существует
echo $user2->getName() . " — доступ: " . ($user2->hasAccess() ? 'да' : 'нет') . PHP_EOL;