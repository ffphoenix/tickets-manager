<?php

namespace App\Users\Infrastructure\Persistence;

use App\Shared\Domain\Exception\NotFound;
use App\Users\Domain\Entity\User;
use App\Users\Domain\UserRepository;
use App\Users\Domain\ValueObject\Email;
use App\Users\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function get(UserId $id): User
    {
        $entity = $this->em->find(User::class, $id);
        if (!$entity) {
            throw new NotFound('User not found: ' . (string) $id);
        }
        return $entity;
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => (string) $email]);
    }

    public function findByGoogleId(string $googleId): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleId]);
    }

    public function exists(UserId $id): bool
    {
        return (bool) $this->em->getRepository(User::class)->find($id);
    }
}
