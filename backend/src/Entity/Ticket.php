<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $eventId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $organiserId;

    #[ORM\Column(type: 'integer')]
    private int $priceCents;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $eventId,
        string $organiserId,
        int $priceCents,
        string $status,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->organiserId = $organiserId;
        $this->priceCents = $priceCents;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getOrganiserId(): string
    {
        return $this->organiserId;
    }

    public function getPriceCents(): int
    {
        return $this->priceCents;
    }

    public function setPriceCents(int $priceCents): void
    {
        $this->priceCents = $priceCents;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
