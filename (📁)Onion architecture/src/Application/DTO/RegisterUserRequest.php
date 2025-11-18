<?php
declare(strict_types=1);

namespace Application\DTO;

class RegisterUserRequest
{
    public function __construct(
        public string $email,
        public string $name
    ) {}
}