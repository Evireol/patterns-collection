<?php
declare(strict_types=1);

// Загружаем "контейнер"
$container = require_once __DIR__ . '/../src/bootstrap.php';

use Application\DTO\RegisterUserRequest;
use Domain\Exception\UserAlreadyExistsException;

// Простой маршрутизатор
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $request = new RegisterUserRequest($_POST['email'], $_POST['name']);
        $container['usecase.register_user']->execute($request);
        echo "<h2>✅ Пользователь зарегистрирован!</h2>";
        echo "<a href='/'>← Назад</a>";
    } catch (UserAlreadyExistsException $e) {
        http_response_code(400);
        echo "<h2>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</h2>";
        echo "<a href='/'>← Назад</a>";
    } catch (\Exception $e) {
        http_response_code(500);
        echo "<h2>500 — Внутренняя ошибка</h2>";
    }

} elseif ($path === '/users') {
    // Демонстрация: вывод всех пользователей
    $users = $container['user.repository']->getAll();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        array_map(fn($u) => ['email' => $u->email, 'name' => $u->name], $users),
        JSON_UNESCAPED_UNICODE
    );

} else {
    // Главная страница
    echo '<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Onion Architecture — Demo</title></head>
<body>
<h1>🧅 Onion Architecture — рабочий пример</h1>

<h2>Регистрация пользователя</h2>
<form method="POST" action="/register">
  Email: <input name="email" type="email" required><br><br>
  Имя: <input name="name" required><br><br>
  <button type="submit">Зарегистрировать</button>
</form>

<hr>
<h2>Просмотр всех пользователей</h2>
<p><a href="/users">GET /users → JSON</a></p>

<hr>
<p><small>Все данные хранятся в памяти (InMemoryUserRepository).<br>
Бизнес-логика полностью отделена от инфраструктуры.</small></p>
</body>
</html>';
}