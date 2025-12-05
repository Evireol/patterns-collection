<?php

class UserService {
    public function createUser(string $name): void {
        // Валидация (если нужно)
        if (empty($name)) {
            throw new InvalidArgumentException('Name is required');
        }

        // Сохранение
        echo "Saving $name to DB\n";
    }
}

$service = new UserService();
$service->createUser("Alice");