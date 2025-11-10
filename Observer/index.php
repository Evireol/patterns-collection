<?php

// Интерфейс наблюдателя
interface Observer
{
    public function update(string $news): void;
}

// Субъект
class NewsPublisher
{
    private array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }

    public function notify(string $news): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($news);
        }
    }

    public function publish(string $news): void
    {
        echo "[Publisher] Новая статья: {$news}\n";
        $this->notify($news);
    }
}

// Наблюдатели
class EmailSubscriber implements Observer
{
    public function __construct(private string $email) {}

    public function update(string $news): void
    {
        echo "[Email] Отправлено на {$this->email}: {$news}\n";
    }
}

class SMSSubscriber implements Observer
{
    public function __construct(private string $phone) {}

    public function update(string $news): void
    {
        echo "[SMS] Отправлено на {$this->phone}: {$news}\n";
    }
}

// Использование
$publisher = new NewsPublisher();

$alice = new EmailSubscriber('alice@example.com');
$bob = new SMSSubscriber('+79991234567');

$publisher->attach($alice);
$publisher->attach($bob);

$publisher->publish('Вышел новый паттерн: Observer!');

// Отписка
$publisher->detach($alice);
$publisher->publish('Теперь только SMS-подписчики получат это.');