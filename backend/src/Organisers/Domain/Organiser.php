<?php

namespace App\Organisers\Domain;

final class Organiser
{
    public function __construct(
        private readonly OrganiserId $id,
        private string $name,
        private readonly \DateTimeImmutable $createdAt,
    ) {
        $this->rename($name);
    }

    public static function create(OrganiserId $id, string $name): self
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Organiser name cannot be empty');
        }
        return new self($id, $name, new \DateTimeImmutable());
    }

    public function id(): OrganiserId { return $this->id; }
    public function name(): string { return $this->name; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function rename(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Organiser name cannot be empty');
        }
        $this->name = $name;
    }
}
