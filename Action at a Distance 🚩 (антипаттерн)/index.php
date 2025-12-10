<?php

// ===== КЛАСС ПОЛЬЗОВАТЕЛЯ (неизменяемый) =====
class User {
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role
    ) {}
}

// ===== СЕРВИС АУТЕНТИФИКАЦИИ =====
class AuthService {
    public function authenticate(string $login, string $password): ?User {
        if ($login === 'admin' && $password === '123') {
            return new User(1, 'Админ', 'admin@example.com', 'admin');
        }
        return null;
    }
}

// ===== СЕРВИС EMAIL =====
class EmailService {
    public function sendWelcome(User $user): void {
        echo "📧 Отправляем письмо на {$user->email}...\n";
        // mail($user->email, 'Добро пожаловать!', '...');
    }
}

// ===== СЕРВИС ЛОГИРОВАНИЯ =====
class Logger {
    public function log(string $userName, string $action): void {
        echo "[LOG] {$userName} выполнил: {$action}\n";
    }
}

// ===== ОСНОВНАЯ ЛОГИКА (явные зависимости) =====
$auth = new AuthService();
$emailService = new EmailService();
$logger = new Logger();

// Логируем как гость
$logger->log('Гость', 'Попытка входа');

$user = $auth->authenticate('admin', '123');

if ($user) {
    $logger->log($user->name, 'Успешный вход');
    $emailService->sendWelcome($user); // ← Явно передаём пользователя
} else {
    echo "Ошибка входа\n";
}

// 🔒 Невозможно случайно подменить пользователя — он передаётся явно!
// 🔒 Каждый сервис ничего не знает о глобальном состоянии.