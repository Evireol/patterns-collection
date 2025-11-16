<?php
// Работает по адресу: http://designpatternsexamples/Front%20Controller/

// === 1. Общая инициализация ===
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // В проде: 0 + логирование

// === 2. Нормализация пути запроса ===
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Удаляем всё до последней части пути: оставляем только то, что после "Front Controller/"
// Пример: /Front%20Controller/api/users → /api/users
$baseUrl = '/Front%20Controller/';
if (strpos($requestUri, $baseUrl) === 0) {
    $path = substr($requestUri, strlen($baseUrl));
} else {
    // На случай, если пришёл как /Front Controller/ (пробел вместо %20)
    $baseUrlFallback = '/Front Controller/';
    if (strpos($requestUri, $baseUrlFallback) === 0) {
        $path = substr($requestUri, strlen($baseUrlFallback));
    } else {
        // Если не совпадает — считаем, что путь корневой
        $path = ltrim(parse_url($requestUri, PHP_URL_PATH), '/');
    }
}

// Нормализуем путь: всегда начинается с /
$path = '/' . ltrim($path, '/');

// Убираем параметры запроса (если есть ?foo=bar)
if (false !== $qpos = strpos($path, '?')) {
    $path = substr($path, 0, $qpos);
}

// === 3. Фейковые зависимости (замените в проде на реальные) ===
class FakeDatabase {
    public function query(string $sql): array {
        return [
            ['id' => 1, 'name' => 'Алиса'],
            ['id' => 2, 'name' => 'Боб']
        ];
    }
}

class FakeLogger {
    public function log(string $message): void {
        error_log("[FRONT_CONTROLLER] " . date('c') . " $message");
    }
}

// === 4. Контроллеры ===
class UserController {
    public function __construct(private FakeDatabase $db, private FakeLogger $logger) {}

    public function listUsers(): void {
        $this->logger->log('Запрос списка пользователей');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->db->query('SELECT * FROM users'), JSON_UNESCAPED_UNICODE);
    }
}

class AuthController {
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Простая имитация
            echo "<h2>✅ Вход выполнен (демо)</h2>";
            echo "<p>В реальном приложении здесь была бы аутентификация.</p>";
        } else {
            echo '<h2>Вход</h2>
                  <form method="POST">
                      Логин: <input name="login" required><br><br>
                      Пароль: <input type="password" name="password" required><br><br>
                      <button type="submit">Войти</button>
                  </form>';
        }
    }
}

// === 5. Роутинг ===
try {
    switch ($path) {
        case '/':
            echo '<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Front Controller</title></head>
<body>
<h1>✅ Front Controller — работает!</h1>
<p>Это единая точка входа для всех запросов.</p>
<ul>
    <li><a href="api/users">GET /api/users</a> — список пользователей (JSON)(открываться не должно это пример)</li>
    <li><a href="login">/login</a> — форма входа (открываться не должно это пример)</li> 
</ul>
<hr>
<p><small>Пример паттерна проектирования «Front Controller»</small></p>
</body>
</html>';
            break;

        case '/api/users':
            if ($method === 'GET') {
                $controller = new UserController(new FakeDatabase(), new FakeLogger());
                $controller->listUsers();
            } else {
                http_response_code(405);
                echo "Метод не разрешён. Используйте GET.";
            }
            break;

        case '/login':
            $controller = new AuthController();
            $controller->login();
            break;

        default:
            http_response_code(404);
            echo "<h2>404 — Страница не найдена</h2>
                  <p>Запрошенный путь: <code>$path</code></p>
                  <p><a href='./'>← На главную</a></p>";
    }

} catch (Exception $e) {
    error_log("Front Controller error: " . $e->getMessage());
    http_response_code(500);
    echo "<h2>500 — Внутренняя ошибка сервера</h2>";
    // В проде не выводите $e->getMessage()!
}