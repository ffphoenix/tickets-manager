<?php

namespace App\Tickets\Application\CreateTicket;

final class CreateTicket
{
    public function __construct(
        public readonly string $ticketId,
        public readonly string $eventId,
        public readonly string $organiserId,
        public readonly int $priceCents,
    ) {}
}
