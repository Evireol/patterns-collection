<?php
abstract class Handler
{
    protected ?Handler $next = null;

    public function setNext(Handler $handler): Handler
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(string $field, mixed $value): ?string
    {
        if ($this->next) {
            return $this->next->handle($field, $value);
        }
        return null; // никто не обработал
    }
}

class RequiredFieldHandler extends Handler
{
    public function handle(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return "Field '{$field}' is required.";
        }
        return parent::handle($field, $value);
    }
}

class EmailFormatHandler extends Handler
{
    public function handle(string $field, mixed $value): ?string
    {
        if (is_string($value) && str_contains($field, 'email')) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return "Field '{$field}' must be a valid email.";
            }
        }
        return parent::handle($field, $value);
    }
}

// Использование
$chain = new RequiredFieldHandler();
$chain->setNext(new EmailFormatHandler());

echo $chain->handle('email', '') ?? 'OK';        // "Field 'email' is required."
echo $chain->handle('email', 'invalid') ?? 'OK'; // "Field 'email' must be a valid email."
echo $chain->handle('email', 'user@example.com') ?? 'OK'; // OK