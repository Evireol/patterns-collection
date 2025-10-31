<?php

// --------------------------------------------------
// 1. ТВОЙ ИНТЕРФЕЙС — единый контракт для всех платёжных систем
// --------------------------------------------------
interface PaymentGateway
{
    public function charge(string $customerId, float $amount): bool;
}

// --------------------------------------------------
// 2. ТВОЙ БИЗНЕС-СЕРВИС — он НЕ знает, какой шлюз используется
// --------------------------------------------------
class SubscriptionService
{
    public function __construct(
        private PaymentGateway $gateway
    ) {}

    public function renewSubscription(string $userId, float $price): void
    {
        echo "🔁 Пробуем продлить подписку для {$userId} на {$price} руб...\n";

        $success = $this->gateway->charge($userId, $price);

        if ($success) {
            echo "✅ Оплата прошла успешно!\n";
        } else {
            echo "❌ Оплата не удалась.\n";
        }
    }
}

// --------------------------------------------------
// 3. СТОРОННИЙ КЛАСС (например, из SDK Stripe или другого провайдера)
//    — ты НЕ можешь его менять!
// --------------------------------------------------
class ExternalPaymentProcessor
{
    // Обрати внимание: метод называется иначе, параметры другие!
    public function processPayment(string $clientToken, int $cents): string
    {
        // Имитация: 80% успеха
        $success = (rand(1, 10) <= 8);
        $status = $success ? 'success' : 'failed';

        echo "[EXTERNAL] Обработка платежа для токена {$clientToken}, сумма: {$cents} центов → {$status}\n";

        return $status;
    }
}

// --------------------------------------------------
// 4. АДАПТЕР — связывает ExternalPaymentProcessor с твоим интерфейсом
// --------------------------------------------------
class ExternalPaymentAdapter implements PaymentGateway
{
    public function __construct(
        private ExternalPaymentProcessor $processor
    ) {}

    public function charge(string $customerId, float $amount): bool
    {
        // Преобразуем входные данные под формат внешнего SDK:
        $clientToken = "tok_{$customerId}";     // придумываем токен из ID
        $cents = (int)($amount * 100);          // рубли → центы

        // Вызываем сторонний метод
        $result = $this->processor->processPayment($clientToken, $cents);

        // Преобразуем результат обратно в bool
        return $result === 'success';
    }
}

// --------------------------------------------------
// 5. ДЕМОНСТРАЦИЯ РАБОТЫ
// --------------------------------------------------

// Создаём сторонний процессор (как будто подключили SDK)
$externalProcessor = new ExternalPaymentProcessor();

// Оборачиваем его в адаптер
$gateway = new ExternalPaymentAdapter($externalProcessor);

// Передаём в бизнес-логику — она ничего не знает о внешнем SDK!
$subscriptionService = new SubscriptionService($gateway);

// Пробуем продлить подписку
$subscriptionService->renewSubscription('user_123', 299.90);

echo "\n---\n";

// А теперь представь: ты подключаешь PayPal!
// Создаёшь PayPalAdapter — и меняешь только эту строку:
// $gateway = new PayPalAdapter(new PayPalClient());
// Весь остальной код остаётся прежним.

//🔁 Пробуем продлить подписку для user_123 на 299.9 руб...
//[EXTERNAL] Обработка платежа для токена tok_user_123, сумма: 29990 центов → success
//✅ Оплата прошла успешно!

//Иногда будет failed — из-за рандома