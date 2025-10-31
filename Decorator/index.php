<?php

// Интерфейс компонента
interface Notifier
{
    public function send(string $message): string;
}

// Базовый компонент — простой отправщик уведомлений
class BasicNotifier implements Notifier
{
    public function send(string $message): string
    {
        return "Sending message: '{$message}'";
    }
}

// Абстрактный декоратор
abstract class NotifierDecorator implements Notifier
{
    protected Notifier $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    // По умолчанию делегируем вызов
    public function send(string $message): string
    {
        return $this->notifier->send($message);
    }
}

// Декоратор: логирование
class LoggingNotifier extends NotifierDecorator
{
    public function send(string $message): string
    {
        $result = $this->notifier->send($message);
        $log = "[LOG] " . date('Y-m-d H:i:s') . " — Message sent\n";
        file_put_contents('notification.log', $log, FILE_APPEND);
        return $result;
    }
}

// Декоратор: шифрование
class EncryptedNotifier extends NotifierDecorator
{
    public function send(string $message): string
    {
        $encrypted = base64_encode($message); // упрощённое "шифрование"
        return $this->notifier->send("ENCRYPTED: {$encrypted}");
    }
}

// Декоратор: отправка по SMS
class SmsNotifier extends NotifierDecorator
{
    public function send(string $message): string
    {
        $smsResult = "[SMS SENT] " . $message;
        return $this->notifier->send($message) . "\n" . $smsResult;
    }
}

// =============== ИСПОЛЬЗОВАНИЕ ===============

// Создаём базовый отправщик
$notifier = new BasicNotifier();

// Оборачиваем в декораторы
$notifier = new LoggingNotifier($notifier);      // Логируем
$notifier = new EncryptedNotifier($notifier);    // Шифруем
$notifier = new SmsNotifier($notifier);          // Отправляем SMS

// Отправляем сообщение
echo $notifier->send("Hello, world!");

// После запуска в той же папке появится файл notification.log