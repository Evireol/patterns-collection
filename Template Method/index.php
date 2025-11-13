<?php

// Абстрактный базовый класс — шаблонный метод
abstract class CaffeineBeverage
{
    // Шаблонный метод — final, чтобы подклассы не могли его изменить
    final public function prepare(): void
    {
        $this->boilWater();
        $this->brew();
        $this->pourInCup();
        if ($this->wantsCondiments()) {
            $this->addCondiments();
        }
    }

    // Общие шаги (можно не переопределять)
    private function boilWater(): void
    {
        echo "Кипячение воды\n";
    }

    private function pourInCup(): void
    {
        echo "Наливаем в чашку\n";
    }

    // Шаги, которые должны реализовать подклассы
    abstract protected function brew(): void;
    abstract protected function addCondiments(): void;

    // Хук (можно переопределить, но не обязательно)
    protected function wantsCondiments(): bool
    {
        return true;
    }
}

// Конкретные реализации
class Tea extends CaffeineBeverage
{
    protected function brew(): void
    {
        echo "Завариваем чай\n";
    }

    protected function addCondiments(): void
    {
        echo "Добавляем лимон\n";
    }
}

class Coffee extends CaffeineBeverage
{
    protected function brew(): void
    {
        echo "Пропускаем горячую воду через кофе\n";
    }

    protected function addCondiments(): void
    {
        echo "Добавляем сахар и молоко\n";
    }

    // Переопределяем хук: не все хотят добавки
    protected function wantsCondiments(): bool
    {
        return false; // допустим, клиент не любит добавки
    }
}

// Использование
echo "=== Готовим чай ===\n";
$tea = new Tea();
$tea->prepare();

echo "\n=== Готовим кофе (без добавок) ===\n";
$coffee = new Coffee();
$coffee->prepare();