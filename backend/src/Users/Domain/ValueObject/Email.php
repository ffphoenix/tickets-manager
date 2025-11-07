<?php

namespace App\Users\Domain\ValueObject;

final class Email
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = strtolower($value);
    }

    public static function fromString(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email: ' . $email);
        }
        return new self($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
