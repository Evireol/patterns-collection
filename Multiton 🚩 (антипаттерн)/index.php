<?php
class Multiton
{
    // Хранилище экземпляров: ключ → объект
    private static array $instances = [];

    // Приватный конструктор
    private function __construct() {}

    // Запрещаем клонирование
    private function __clone() {}

    // Запрещаем десериализацию
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a Multiton");
    }

    // Основной метод получения экземпляра по ключу
    public static function getInstance(string $key): self
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self();
        }
        return self::$instances[$key];
    }

    // Вспомогательный метод для демонстрации (можно убрать)
    public function identify(): string
    {
        return spl_object_hash($this);
    }
}

// Использование
$loggerA = Multiton::getInstance('logger.A');
$loggerB = Multiton::getInstance('logger.B');
$loggerA2 = Multiton::getInstance('logger.A');

echo $loggerA->identify() . "\n";   // например: 000000007f8b1c1a0000000012345678
echo $loggerB->identify() . "\n";   // другой хеш
echo $loggerA2->identify() . "\n";  // тот же, что у $loggerA

var_dump($loggerA === $loggerA2);   // bool(true)
var_dump($loggerA === $loggerB);    // bool(false)