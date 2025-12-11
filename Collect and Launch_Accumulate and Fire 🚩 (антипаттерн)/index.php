<?php

// ===== ФУНКЦИЯ: все зависимости — в параметрах =====
function calculateOrderTotal(
    array $items,
    float $discountRate,   // ← явно передаём
    float $taxRate,        // ← явно передаём
    string $currency       // ← явно передаём
): string {
    if (empty($items)) {
        throw new InvalidArgumentException("Список товаров пуст");
    }

    if ($discountRate < 0 || $discountRate > 100) {
        throw new InvalidArgumentException("Скидка должна быть от 0 до 100%");
    }
    if ($taxRate < 0) {
        throw new InvalidArgumentException("Налог не может быть отрицательным");
    }

    $subtotal = array_sum(array_column($items, 'price'));
    $discounted = $subtotal * (1 - $discountRate / 100);
    $total = $discounted * (1 + $taxRate / 100);

    return number_format($total, 2, ',', ' ') . " $currency";
}

// ===== ИСПОЛЬЗОВАНИЕ =====
$cart = [
    ['name' => 'Книга', 'price' => 1000],
    ['name' => 'Блокнот', 'price' => 300]
];

// Явно указываем все параметры
echo calculateOrderTotal($cart, 10, 20, 'EUR'); 
// Вывод: "1 188,00 EUR"

// Меняем параметры — без риска
echo calculateOrderTotal($cart, 0, 10, 'USD'); 
// Вывод: "1 430,00 USD"

// А теперь — легко тестируем:
// test1: calculateOrderTotal($cart, 0, 0, 'RUB') → 1300
// test2: calculateOrderTotal($cart, 100, 0, 'RUB') → 0