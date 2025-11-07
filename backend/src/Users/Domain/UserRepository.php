<?php

namespace App\Users\Domain;

use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\Email;
use App\Users\Domain\ValueObject\UserId;
use App\Shared\Domain\Exception\NotFound;

interface UserRepository
{
    public function save(User $user): void;

    /** @throws NotFound */
    public function get(UserId $id): User;

    public function findByEmail(Email $email): ?User;

    public function findByGoogleId(string $googleId): ?User;

    public function exists(UserId $id): bool;
}
