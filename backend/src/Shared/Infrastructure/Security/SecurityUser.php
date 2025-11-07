<?php

namespace App\Shared\Infrastructure\Security;

use App\Users\Domain\Entity\User as DomainUser;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityUser implements UserInterface
{
    public function __construct(private readonly DomainUser $domainUser)
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->domainUser->id();
    }

    public function getRoles(): array
    {
        $roles = array_map(static fn($r) => 'ROLE_' . strtoupper($r->value), $this->domainUser->roles());
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function domain(): DomainUser
    {
        return $this->domainUser;
    }
}
