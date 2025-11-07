<?php

namespace App\Users\Domain\Entity;

use App\Users\Domain\UserRole;
use App\Users\Domain\ValueObject\Email;
use App\Users\Domain\ValueObject\UserId;

final class User
{
    public function __construct(
        private UserId $id,
        private Email $email,
        private ?string $displayName,
        private ?string $googleId,
        /** @var string[] */
        private array $roles,
        private \DateTimeImmutable $createdAt,
    ) {}

    public static function register(UserId $id, Email $email, ?string $displayName = null, ?string $googleId = null): self
    {
        return new self($id, $email, $displayName, $googleId, [UserRole::CUSTOMER->value], new \DateTimeImmutable());
    }

    public function id(): UserId { return $this->id; }
    public function email(): Email { return $this->email; }
    public function displayName(): ?string { return $this->displayName; }
    public function googleId(): ?string { return $this->googleId; }
    /** @return UserRole[] */
    public function roles(): array { return array_map(fn(string $r) => UserRole::from($r), $this->roles); }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function rename(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function linkGoogleId(string $googleId): void
    {
        $this->googleId = $googleId;
    }

    public function assignRole(UserRole $role): void
    {
        foreach ($this->roles as $r) {
            if ($r === $role->value) {
                return;
            }
        }
        $this->roles[] = $role->value;
    }

    public function hasRole(UserRole $role): bool
    {
        foreach ($this->roles as $r) {
            if ($r === $role->value) {
                return true;
            }
        }
        return false;
    }
}
