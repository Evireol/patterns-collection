<?php
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public array $permissions = []
    ) {}

    public function __clone()
    {
        // Глубокое копирование массива (иначе будет shared reference)
        $this->permissions = $this->permissions;
    }
}

// Создаём прототип
$adminPrototype = new User('Admin', 'admin@example.com', ['read', 'write', 'delete']);

// Клонируем
$user1 = clone $adminPrototype;
$user1->name = 'Alice';
$user1->email = 'alice@example.com';
$user1->permissions = ['read', 'write']; // можно изменить
print_r($user1);

$user2 = clone $adminPrototype;
$user2->name = 'Bob';
$user2->permissions = ['read'];print_r($user2);