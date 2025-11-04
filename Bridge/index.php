<?php

// =============== Интерфейс реализации (платёжные шлюзы) ===============
interface PaymentGateway
{
    public function charge(float $amount, string $currency, array $customerData): string; // возвращает ID платежа
    public function refund(string $paymentId, float $amount): bool;
    public function cancel(string $paymentId): bool;
}

// =============== Конкретные реализации ===============

class StripeAdapter implements PaymentGateway
{
    public function charge(float $amount, string $currency, array $customerData): string
    {
        // Имитация вызова Stripe API
        echo "[Stripe] Charging {$amount} {$currency} for {$customerData['email']}\n";
        return 'ch_stripe_' . uniqid();
    }

    public function refund(string $paymentId, float $amount): bool
    {
        echo "[Stripe] Refunding {$amount} for payment {$paymentId}\n";
        return true;
    }

    public function cancel(string $paymentId): bool
    {
        echo "[Stripe] Cancelling payment {$paymentId}\n";
        return true;
    }
}

class PayPalAdapter implements PaymentGateway
{
    public function charge(float $amount, string $currency, array $customerData): string
    {
        echo "[PayPal] Processing payment of {$amount} {$currency} for {$customerData['email']}\n";
        return 'txn_paypal_' . uniqid();
    }

    public function refund(string $paymentId, float $amount): bool
    {
        echo "[PayPal] Issuing refund {$amount} on transaction {$paymentId}\n";
        return true;
    }

    public function cancel(string $paymentId): bool
    {
        echo "[PayPal] Voiding authorization {$paymentId}\n";
        return true;
    }
}

class TinkoffAdapter implements PaymentGateway
{
    public function charge(float $amount, string $currency, array $customerData): string
    {
        echo "[Tinkoff] Initiating RUB payment: {$amount} for {$customerData['phone']}\n";
        return 'ord_tinkoff_' . uniqid();
    }

    public function refund(string $paymentId, float $amount): bool
    {
        echo "[Tinkoff] Refunding {$amount} RUB on order {$paymentId}\n";
        return true;
    }

    public function cancel(string $paymentId): bool
    {
        echo "[Tinkoff] Reversing payment {$paymentId}\n";
        return true;
    }
}

// =============== Абстракция (единый интерфейс для бизнес-логики) ===============
abstract class PaymentProcessor
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    abstract public function processPayment(float $amount, string $currency, array $customerData): string;
    abstract public function issueRefund(string $paymentId, float $amount): bool;
    abstract public function cancelPayment(string $paymentId): bool;
}

// =============== Конкретные абстракции (по сценариям) ===============
// Например: одноразовый платёж, подписка, рекуррентный платёж
// В примерне один — StandardPayment

class StandardPayment extends PaymentProcessor
{
    public function processPayment(float $amount, string $currency, array $customerData): string
    {
        // Можно добавить общую логику: валидация, логирование
        echo "[StandardPayment] Validating and processing...\n";
        return $this->gateway->charge($amount, $currency, $customerData);
    }

    public function issueRefund(string $paymentId, float $amount): bool
    {
        echo "[StandardPayment] Requesting refund...\n";
        return $this->gateway->refund($paymentId, $amount);
    }

    public function cancelPayment(string $paymentId): bool
    {
        echo "[StandardPayment] Cancelling payment...\n";
        return $this->gateway->cancel($paymentId);
    }
}

// =============== Использование ===============

// Данные клиента
$customer = [
    'email' => 'user@example.com',
    'phone' => '+79991234567',
    'name'  => 'Alex'
];

echo "=== Stripe Payment ===\n";
$stripeProcessor = new StandardPayment(new StripeAdapter());
$paymentId = $stripeProcessor->processPayment(99.99, 'USD', $customer);
$stripeProcessor->issueRefund($paymentId, 50.00);

echo "\n=== Tinkoff Payment ===\n";
$tinkoffProcessor = new StandardPayment(new TinkoffAdapter());
$paymentId2 = $tinkoffProcessor->processPayment(5000, 'RUB', $customer);
$tinkoffProcessor->cancelPayment($paymentId2);

echo "\n=== PayPal Payment ===\n";
$paypalProcessor = new StandardPayment(new PayPalAdapter());
$paymentId3 = $paypalProcessor->processPayment(49.90, 'EUR', $customer);
$paypalProcessor->issueRefund($paymentId3, 49.90);

//Пример вывода
//=== Stripe Payment ===
//[StandardPayment] Validating and processing...
//[Stripe] Charging 99.99 USD for user@example.com
//[StandardPayment] Requesting refund...
//[Stripe] Refunding 50 on payment ch_stripe_672a1b2c3d4e5

//=== Tinkoff Payment ===
//[StandardPayment] Validating and processing...
//[Tinkoff] Initiating RUB payment: 5000 for +79991234567
//[StandardPayment] Cancelling payment...
//[Tinkoff] Reversing payment ord_tinkoff_672a1b2c3f6g7

//=== PayPal Payment ===
//[StandardPayment] Validating and processing...
//[PayPal] Processing payment of 49.9 EUR for user@example.com
//[StandardPayment] Requesting refund...
//[PayPal] Issuing refund 49.9 on transaction txn_paypal_672a1b2c3h8i9