<?php

// =============== Интерфейс стратегии ===============
interface SortingStrategy
{
    /**
     * @param array $users — массив ассоциативных массивов с ключами: id, name, age
     * @return array — отсортированный массив
     */
    public function sort(array $users): array;
}

// =============== Конкретные стратегии ===============

class SortByName implements SortingStrategy
{
    public function sort(array $users): array
    {
        usort($users, fn($a, $b) => strcmp($a['name'], $b['name']));
        return $users;
    }
}

class SortByAge implements SortingStrategy
{
    public function sort(array $users): array
    {
        usort($users, fn($a, $b) => $a['age'] <=> $b['age']);
        return $users;
    }
}

class SortById implements SortingStrategy
{
    public function sort(array $users): array
    {
        usort($users, fn($a, $b) => $a['id'] <=> $b['id']);
        return $users;
    }
}

// =============== Контекст (клиент) ===============
class UserList
{
    private array $users;
    private SortingStrategy $strategy;

    public function __construct(array $users, SortingStrategy $strategy)
    {
        $this->users = $users;
        $this->strategy = $strategy;
    }

    public function setSortingStrategy(SortingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getSortedUsers(): array
    {
        return $this->strategy->sort($this->users);
    }

    public function printUsers(): void
    {
        $sorted = $this->getSortedUsers();
        foreach ($sorted as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Age: {$user['age']}\n";
        }
    }
}

// =============== Демонстрация ===============

$users = [
    ['id' => 3, 'name' => 'Alice', 'age' => 30],
    ['id' => 1, 'name' => 'Bob', 'age' => 25],
    ['id' => 2, 'name' => 'Charlie', 'age' => 35],
];

echo "=== Сортировка по ID ===\n";
$userList = new UserList($users, new SortById());
$userList->printUsers();

echo "\n=== Сортировка по имени ===\n";
$userList->setSortingStrategy(new SortByName());
$userList->printUsers();

echo "\n=== Сортировка по возрасту ===\n";
$userList->setSortingStrategy(new SortByAge());
$userList->printUsers();