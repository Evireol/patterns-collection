<?php
declare(strict_types=1);

namespace Application\UseCase;

use Domain\Repository\UserRepositoryInterface;
use Application\DTO\RegisterUserRequest;
use Domain\Exception\UserAlreadyExistsException;

class RegisterUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(RegisterUserRequest $request): void
    {
        if ($this->userRepository->findByEmail($request->email)) {
            throw new UserAlreadyExistsException('User with this email already exists.');
        }

        $user = new \Domain\Entity\User($request->email, $request->name);
        $this->userRepository->save($user);
    }
}