<?php

// 1. Интерфейс — контракт для всех уведомителей
interface NotificationService
{
    public function send(string $to, string $message): void;
}

// 2. Отправка по email
class EmailNotifier implements NotificationService
{
    public function send(string $to, string $message): void
    {
        echo "[EMAIL] Отправлено на {$to}: {$message}\n";
    }
}

// 3. Отправка в Slack
class SlackNotifier implements NotificationService
{
    public function send(string $to, string $message): void
    {
        // В Slack $to — это канал или ID пользователя
        echo "[SLACK] Отправлено в {$to}: {$message}\n";
    }
}

// 4. Комбинированный отправитель: email + Slack
class MultiChannelNotifier implements NotificationService
{
    // Конструктор с promoted properties (PHP 8.0+)
    public function __construct(
        private EmailNotifier $email,
        private SlackNotifier $slack
    ) {}

    public function send(string $to, string $message): void
    {
        // Отправляем email на указанный адрес
        $this->email->send($to, $message);

        // Отправляем в Slack — в общий канал, но с упоминанием получателя
        $slackMessage = "Уведомление для {$to}: {$message}";
        $this->slack->send('#orders', $slackMessage);
    }
}

// 5. Бизнес-сервис: обработка заказа
class OrderService
{
    public function __construct(
        private NotificationService $notifier
    ) {}

    public function completeOrder(string $customerEmail): void
    {
        echo "✅ Заказ завершён для {$customerEmail}\n";

        // Используем абстракцию — не зная, куда именно уйдёт уведомление
        $this->notifier->send($customerEmail, 'Ваш заказ успешно обработан!');
    }
}

// --------------------------------------------------
// 6. ДЕМОНСТРАЦИЯ РАБОТЫ
// --------------------------------------------------

// Создаём реальные отправители
$emailSender = new EmailNotifier();
$slackSender = new SlackNotifier();

// Комбинируем их
$multiNotifier = new MultiChannelNotifier($emailSender, $slackSender);

// Создаём сервис заказа с комбинированным уведомителем
$orderService = new OrderService($multiNotifier);

// Завершаем заказ
$orderService->completeOrder('alice@example.com');

echo "\n---\n";

// А теперь — только email (для сравнения)
$emailOnlyService = new OrderService($emailSender);
$emailOnlyService->completeOrder('bob@example.com');