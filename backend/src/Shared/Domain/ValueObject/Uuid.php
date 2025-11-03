<?php

namespace App\Shared\Domain\ValueObject;

/**
 * Simple UUID value object (v4 by default).
 * - Provides validation and generation without external deps.
 */
class Uuid
{
    protected string $value;

    final public function __construct(string $value)
    {
        $this->assertValid($value);
        $this->value = strtolower($value);
    }

    public static function v4(): static
    {
        $bytes = random_bytes(16);
        // Set version to 0100
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        // Set variant to 10xx
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $hex = bin2hex($bytes);
        $uuid = sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
        return new static($uuid);
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    protected function assertValid(string $value): void
    {
        if (!preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $value
        )) {
            throw new \InvalidArgumentException('Invalid UUID: ' . $value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
