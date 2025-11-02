<?php

// =============== Интерфейсы ===============

interface InventoryServiceInterface
{
    public function isInStock(int $productId): bool;
}

interface PricingServiceInterface
{
    public function calculate(int $productId, int $userId): float;
}

interface InvoiceServiceInterface
{
    public function create(int $userId, int $productId, float $amount): string;
}

interface NotificationServiceInterface
{
    public function send(string $message, int $userId): void;
}

// =============== Реализации сервисов ===============

class InventoryService implements InventoryServiceInterface
{
    public function isInStock(int $productId): bool
    {
        // Упрощённая логика: товар 101 — есть, остальные — нет
        return $productId === 101;
    }
}

class PricingService implements PricingServiceInterface
{
    public function calculate(int $productId, int $userId): float
    {
        // Базовая цена + скидка для пользователя 1001
        $basePrice = 100.0;
        return $userId === 1001 ? $basePrice * 0.9 : $basePrice;
    }
}

class InvoiceService implements InvoiceServiceInterface
{
    public function create(int $userId, int $productId, float $amount): string
    {
        $invoiceId = 'INV-' . uniqid();
        echo "[Invoice] Created {$invoiceId} for user {$userId}, product {$productId}, amount {$amount}\n";
        return $invoiceId;
    }
}

class NotificationService implements NotificationServiceInterface
{
    public function send(string $message, int $userId): void
    {
        echo "[Notification] Sent to user {$userId}: {$message}\n";
    }
}

// =============== Фасад ===============

class CheckoutFacade
{
    public function __construct(
        private InventoryServiceInterface $inventory,
        private PricingServiceInterface $pricing,
        private InvoiceServiceInterface $invoice,
        private NotificationServiceInterface $notification
    ) {}

    public function processOrder(int $userId, int $productId): string
    {
        if (!$this->inventory->isInStock($productId)) {
            throw new Exception("Product {$productId} is out of stock.");
        }

        $price = $this->pricing->calculate($productId, $userId);
        $invoiceId = $this->invoice->create($userId, $productId, $price);
        $this->notification->send("Your order #{$invoiceId} has been confirmed!", $userId);

        return $invoiceId;
    }
}

// =============== Использование ===============

try {
    // Создаём зависимости вручную (в реальном проекте — через DI-контейнер)
    $inventory = new InventoryService();
    $pricing = new PricingService();
    $invoice = new InvoiceService();
    $notification = new NotificationService();

    $checkout = new CheckoutFacade($inventory, $pricing, $invoice, $notification);

    // Успешный заказ
    $invoiceId = $checkout->processOrder(userId: 1001, productId: 101);
    echo "✅ Order successful! Invoice: {$invoiceId}\n";

    // Попытка заказать отсутствующий товар
    // $checkout->processOrder(1002, 999); // выбросит исключение

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// =============== Пример unit-теста (в том же файле для демонстрации) ===============

echo "\n--- Running simple test ---\n";

// Создаём mock-уведомление (записываем вызовы)
$notificationLog = [];
$mockNotification = new class($notificationLog) implements NotificationServiceInterface {
    public function __construct(private array &$log) {}
    public function send(string $message, int $userId): void {
        $this->log[] = compact('message', 'userId');
    }
};

// Собираем фасад с моками
$checkoutForTest = new CheckoutFacade(
    new InventoryService(),
    new PricingService(),
    new InvoiceService(),
    $mockNotification
);

$invoiceId = $checkoutForTest->processOrder(1001, 101);
$lastNotification = end($notificationLog);

if ($lastNotification['userId'] === 1001 && str_contains($lastNotification['message'], $invoiceId)) {
    echo "✅ Test passed: Notification contains correct invoice ID.\n";
} else {
    echo "❌ Test failed.\n";
}