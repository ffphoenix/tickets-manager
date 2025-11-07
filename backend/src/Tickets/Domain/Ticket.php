<?php

namespace App\Tickets\Domain;

use App\Events\Domain\ValueObject\EventId;
use App\Organisers\Domain\ValueObject\OrganiserId;

final class Ticket
{
    public function __construct(
        private readonly TicketId $id,
        private readonly EventId $eventId,
        private readonly OrganiserId $organiserId,
        private int $priceCents,
        private TicketStatus $status,
        private readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function create(TicketId $id, EventId $eventId, OrganiserId $organiserId, int $priceCents): self
    {
        if ($priceCents < 0) {
            throw new \InvalidArgumentException('priceCents must be >= 0');
        }
        return new self($id, $eventId, $organiserId, $priceCents, TicketStatus::CREATED, new \DateTimeImmutable());
    }

    public function id(): TicketId { return $this->id; }
    public function eventId(): EventId { return $this->eventId; }
    public function organiserId(): OrganiserId { return $this->organiserId; }
    public function priceCents(): int { return $this->priceCents; }
    public function status(): TicketStatus { return $this->status; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function changePrice(int $priceCents): void
    {
        if ($priceCents < 0) {
            throw new \InvalidArgumentException('priceCents must be >= 0');
        }
        $this->priceCents = $priceCents;
    }

    public function reserve(): void
    {
        if ($this->status !== TicketStatus::CREATED) {
            throw new \DomainException('Only newly created tickets can be reserved');
        }
        $this->status = TicketStatus::RESERVED;
    }

    public function sell(): void
    {
        if (!in_array($this->status, [TicketStatus::CREATED, TicketStatus::RESERVED], true)) {
            throw new \DomainException('Ticket cannot be sold from current status');
        }
        $this->status = TicketStatus::SOLD;
    }

    public function cancel(): void
    {
        if ($this->status === TicketStatus::SOLD) {
            throw new \DomainException('Sold ticket cannot be cancelled');
        }
        $this->status = TicketStatus::CANCELLED;
    }
}
