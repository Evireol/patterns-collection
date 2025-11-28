<?php

// =============== Сущность ===============
class User
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public string $phone
    ) {}
}

// =============== Репозиторий (заглушка вместо реальной БД) ===============
class UserRepository
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->email] = $user;
        echo "[DB] Saved user: {$user->email}\n";
    }

    public function findByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }
}

// =============== Валидатор ===============
class UserValidator
{
    public function validateForRegistration(User $user): array
    {
        $errors = [];
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email';
        }
        if (strlen($user->password) < 8) {
            $errors[] = 'Password too short';
        }
        return $errors;
    }
}

// =============== Сервис уведомлений (заглушка) ===============
class NotificationService
{
    public function sendWelcomeEmail(User $user): void
    {
        echo "[EMAIL] Sent welcome email to: {$user->email}\n";
    }
}

// =============== Простой логгер (реализация PSR-3 не обязательна для примера) ===============
class SimpleLogger
{
    public function error(string $message): void
    {
        echo "[ERROR] $message\n";
    }

    public function info(string $message): void
    {
        echo "[INFO] $message\n";
    }
}

// =============== Основной сервис — оркестратор ===============
class UserRegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserValidator $validator,
        private NotificationService $notifications,
        private SimpleLogger $logger
    ) {}

    public function register(User $user): bool
    {
        $errors = $this->validator->validateForRegistration($user);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->logger->error($error);
            }
            return false;
        }

        if ($this->userRepository->findByEmail($user->email)) {
            $this->logger->error("Email already exists: {$user->email}");
            return false;
        }

        $this->userRepository->save($user);
        $this->notifications->sendWelcomeEmail($user);
        $this->logger->info("User successfully registered: {$user->email}");

        return true;
    }
}

// =============== ЗАПУСК ===============
echo "=== Регистрация пользователя (правильная архитектура) ===\n";

// Создаём зависимости
$userRepo = new UserRepository();
$validator = new UserValidator();
$notifier = new NotificationService();
$logger = new SimpleLogger();

// Создаём сервис
$registrationService = new UserRegistrationService($userRepo, $validator, $notifier, $logger);

// Регистрируем пользователя
$user1 = new User('alice@example.com', 'Alice', 'secure123', '+79991112233');
$registrationService->register($user1);

echo "\n--- Повторная регистрация того же email ---\n";
$registrationService->register($user1); // Должна быть ошибка

echo "\n--- Регистрация с коротким паролем ---\n";
$user2 = new User('bob@example.com', 'Bob', '123', '+79994445566');
$registrationService->register($user2); // Ошибка валидации