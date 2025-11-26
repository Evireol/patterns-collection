<?php

// =========== АНТИПАТТЕРН: Базовый класс-утилита ===========

abstract class BaseUtil
{
    public static function formatPhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class UserServiceBad extends BaseUtil
{
    public function createUser(array $data): string
    {
        if (!$this->validateEmail($data['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
        $phone = $this->formatPhone($data['phone']);
        return "User created: {$data['email']}, phone: $phone";
    }
}

class OrderServiceBad extends BaseUtil
{
    public function processOrder(array $order): string
    {
        $cleanPhone = $this->formatPhone($order['phone']);
        return "Order processed for phone: $cleanPhone";
    }
}

// =========== ХОРОШАЯ АЛЬТЕРНАТИВА: Сервисы + Композиция ===========

class ValidationService
{
    public function formatPhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class UserServiceGood
{
    public function __construct(private ValidationService $validator) {}

    public function createUser(array $data): string
    {
        if (!$this->validator->validateEmail($data['email'])) {
            throw new InvalidArgumentException('Invalid email');
        }
        $phone = $this->validator->formatPhone($data['phone']);
        return "User created (good way): {$data['email']}, phone: $phone";
    }
}

class OrderServiceGood
{
    public function __construct(private ValidationService $validator) {}

    public function processOrder(array $order): string
    {
        $cleanPhone = $this->validator->formatPhone($order['phone']);
        return "Order processed (good way) for phone: $cleanPhone";
    }
}

// =========== ТЕСТИРОВАНИЕ ===========

try {
    echo "=== Использование антипаттерна ===\n";
    $userBad = new UserServiceBad();
    echo $userBad->createUser(['email' => 'test@example.com', 'phone' => '+7 (999) 123-45-67']) . "\n";

    $orderBad = new OrderServiceBad();
    echo $orderBad->processOrder(['phone' => '+1 (555) 987-6543']) . "\n";

    echo "\n=== Использование хорошей альтернативы ===\n";
    $validator = new ValidationService();
    $userGood = new UserServiceGood($validator);
    echo $userGood->createUser(['email' => 'good@example.org', 'phone' => '+7 (888) 000-11-22']) . "\n";

    $orderGood = new OrderServiceGood($validator);
    echo $orderGood->processOrder(['phone' => '+44 20 1234 5678']) . "\n";

} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}