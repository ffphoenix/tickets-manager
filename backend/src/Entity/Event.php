<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'events')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 36)]
    private string $organiserId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $endAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $name,
        string $organiserId,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->organiserId = $organiserId;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOrganiserId(): string
    {
        return $this->organiserId;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
