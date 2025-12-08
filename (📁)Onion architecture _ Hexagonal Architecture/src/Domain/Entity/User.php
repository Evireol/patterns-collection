<?php
declare(strict_types=1);

namespace Domain\Entity;

class User
{
    public function __construct(
        public string $email,
        public string $name
    ) {}
}