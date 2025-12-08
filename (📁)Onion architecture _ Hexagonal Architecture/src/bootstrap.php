<?php
declare(strict_types=1);

require_once __DIR__ . '/Domain/Entity/User.php';
require_once __DIR__ . '/Domain/Repository/UserRepositoryInterface.php';
require_once __DIR__ . '/Domain/Exception/UserAlreadyExistsException.php';
require_once __DIR__ . '/Application/DTO/RegisterUserRequest.php';
require_once __DIR__ . '/Application/UseCase/RegisterUser.php';
require_once __DIR__ . '/Infrastructure/Repository/InMemoryUserRepository.php';

// Создаём зависимости вручную (в реальном проекте — через DI-контейнер)
$container = [];

$container['user.repository'] = new \Infrastructure\Repository\InMemoryUserRepository();
$container['usecase.register_user'] = new \Application\UseCase\RegisterUser(
    $container['user.repository']
);

return $container;